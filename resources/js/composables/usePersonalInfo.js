import { ref, reactive, readonly } from 'vue'
import axios from 'axios'

const personalInfo = ref({})
const isLoading = ref(false)
const error = ref(null)

export function usePersonalInfo() {
  const fetchPersonalInfo = async () => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await axios.get('/api/personal-info/public')
      personalInfo.value = response.data.flat
      
      return response.data
    } catch (err) {
      error.value = err.message
      console.error('Personal info fetch error:', err)
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const getInfo = (key, defaultValue = '') => {
    return personalInfo.value[key] || defaultValue
  }

  const getSkills = () => {
    try {
      const skillsData = personalInfo.value.skills
      if (typeof skillsData === 'string') {
        return JSON.parse(skillsData)
      }
      return skillsData || {}
    } catch (error) {
      console.error('Skills parsing error:', error)
      return {}
    }
  }

  const getSocialLinks = () => {
    return {
      github: getInfo('github_url', ''),
      linkedin: getInfo('linkedin_url', ''),
      twitter: getInfo('twitter_url', ''),
      instagram: getInfo('instagram_url', '')
    }
  }

  const getContactInfo = () => {
    return {
      email: getInfo('email', ''),
      phone: getInfo('phone', ''),
      address: getInfo('address', ''),
      location: getInfo('location', '')
    }
  }

  const getAboutInfo = () => {
    return {
      fullName: getInfo('full_name', ''),
      title: getInfo('title', ''),
      bio: getInfo('bio', ''),
      location: getInfo('location', ''),
      birthDate: getInfo('birth_date', '')
    }
  }

  const getSiteInfo = () => {
    return {
      title: getInfo('site_title', ''),
      description: getInfo('site_description', ''),
      keywords: getInfo('site_keywords', '')
    }
  }

  const getFileUrls = () => {
    return {
      cv: getInfo('cv_url', ''),
      profileImage: getInfo('profile_image', '')
    }
  }

  // Admin functions
  const updatePersonalInfo = async (data) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await axios.put('/api/personal-info', { data })
      
      // Refresh data after update
      await fetchPersonalInfo()
      
      return response.data
    } catch (err) {
      error.value = err.message
      console.error('Personal info update error:', err)
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const updateSingleKey = async (key, value, type = 'text', category = null) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await axios.put(`/api/personal-info/key/${key}`, {
        value,
        type,
        category
      })
      
      // Update local state
      personalInfo.value[key] = value
      
      return response.data
    } catch (err) {
      error.value = err.message
      console.error('Personal info key update error:', err)
      throw err
    } finally {
      isLoading.value = false
    }
  }

  return {
    personalInfo: readonly(personalInfo),
    isLoading: readonly(isLoading),
    error: readonly(error),
    
    // Data fetching
    fetchPersonalInfo,
    
    // Getters
    getInfo,
    getSkills,
    getSocialLinks,
    getContactInfo,
    getAboutInfo,
    getSiteInfo,
    getFileUrls,
    
    // Admin functions
    updatePersonalInfo,
    updateSingleKey
  }
}