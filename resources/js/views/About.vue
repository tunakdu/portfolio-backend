<template>
  <section class="py-24 sm:py-32 bg-gradient-to-b from-white to-slate-50" id="about">
    <div v-if="loading" class="flex items-center justify-center min-h-[50vh]">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Yükleniyor...</p>
      </div>
    </div>

    <div v-else class="mx-auto max-w-7xl px-6 lg:px-8">
      <!-- Hero Section -->
      <div class="mx-auto max-w-4xl text-center mb-20">
        <div class="animate-fade-up">
          <span class="inline-block px-4 py-2 rounded-full bg-blue-100 text-blue-600 text-sm font-medium mb-8">
            🧑‍💻 Hakkımda
          </span>
          

          <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-4">
            {{ aboutData.name }}
          </h1>
          <h2 class="text-2xl md:text-3xl font-semibold text-blue-600 mb-6">
            {{ aboutData.title }}
          </h2>
          
          <!-- Contact Info -->
          <div class="flex flex-wrap justify-center gap-4 mb-8 text-gray-600">
            <div v-if="aboutData.location" class="flex items-center space-x-1">
              <MapPin class="w-4 h-4" />
              <span>{{ aboutData.location }}</span>
            </div>
            <div v-if="aboutData.email" class="flex items-center space-x-1">
              <Mail class="w-4 h-4" />
              <span>{{ aboutData.email }}</span>
            </div>
            <div v-if="aboutData.phone" class="flex items-center space-x-1">
              <Phone class="w-4 h-4" />
              <span>{{ aboutData.phone }}</span>
            </div>
          </div>

          <!-- Social Links -->
          <div v-if="socialLinks.length > 0" class="flex justify-center space-x-4 mb-8">
            <a
              v-for="link in socialLinks"
              :key="link.label"
              :href="link.url"
              target="_blank"
              rel="noopener noreferrer"
              class="w-12 h-12 bg-gray-100 hover:bg-blue-600 rounded-full flex items-center justify-center transition-all duration-300 group transform hover:scale-110"
            >
              <component :is="link.icon" class="w-5 h-5 text-gray-600 group-hover:text-white" />
            </a>
          </div>


          <!-- Professional Summary -->
          <div class="max-w-4xl mx-auto mb-12">
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-8 border border-blue-200">
              <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <span class="mr-2">👨‍💻</span>
                Profesyonel Özet
              </h3>
              <div class="prose prose-lg text-gray-700 text-left">
                <p
                  v-for="(paragraph, index) in bioParagraphs"
                  :key="index"
                  class="mb-3 leading-relaxed animate-fade-up"
                  :style="{ animationDelay: `${index * 0.1}s` }"
                >
                  {{ paragraph }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Skills Grid -->
      <div v-if="skills.length > 0" class="mb-20">
        <div class="text-center mb-12 animate-fade-up">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Teknik Yetenekler
          </h2>
          <p class="text-lg text-gray-600">
            Uzmanlaştığım teknolojiler ve araçlar
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(skill, index) in skills"
            :key="skill.id"
            class="modern-card p-6 hover:shadow-xl transition-all duration-300 animate-fade-up cursor-pointer group"
            :style="{ animationDelay: `${index * 0.1}s` }"
            @click="selectedSkill = skill"
          >
            <div class="flex items-start space-x-4">
              <div :class="`w-16 h-16 rounded-2xl bg-gradient-to-r ${skill.color} p-3 flex items-center justify-center group-hover:scale-110 transition-transform duration-300`">
                <span class="text-2xl">{{ getSkillIcon(skill.icon) }}</span>
              </div>
              
              <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                  {{ skill.title }}
                </h3>
                
                <!-- Proficiency Level -->
                <div class="flex items-center space-x-2 mb-3">
                  <div class="flex space-x-1">
                    <div
                      v-for="i in 5"
                      :key="i"
                      :class="`w-2.5 h-2.5 rounded-full transition-all duration-300 ${
                        i <= skill.proficiency_level ? 'bg-yellow-400 shadow-sm' : 'bg-gray-300'
                      }`"
                    />
                  </div>
                  <span class="text-sm font-medium text-gray-600">
                    {{ getProficiencyLabel(skill.proficiency_level) }}
                  </span>
                </div>

                <!-- Technology Count -->
                <div class="flex items-center text-sm text-gray-500">
                  <span class="mr-1">🛠️</span>
                  <span>{{ (Array.isArray(skill.technologies) ? skill.technologies.length : 0) }} teknoloji</span>
                </div>
              </div>

              <!-- Hover Arrow -->
              <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                  <span class="text-blue-600 text-sm">→</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Experience Timeline -->
      <div v-if="experiences.length > 0" class="mb-20">
        <div class="text-center mb-12 animate-fade-up">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            İş Deneyimi
          </h2>
          <p class="text-lg text-gray-600">
            Profesyonel çalışma geçmişim
          </p>
        </div>

        <div class="relative max-w-6xl mx-auto">
          <!-- Central Timeline Line - Desktop -->
          <div class="absolute left-1/2 transform -translate-x-0.5 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-400 via-blue-500 to-blue-600 hidden md:block"></div>
          
          <!-- Mobile Timeline Line -->
          <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-400 via-blue-500 to-blue-600 md:hidden"></div>
          
          <div class="space-y-8 md:space-y-12">
            <div
              v-for="(exp, index) in sortedExperiences"
              :key="exp.id"
              :class="`relative animate-fade-up ${index % 2 === 0 ? 'md:text-right md:pr-8' : 'md:text-left md:pl-8'} pl-16 md:pl-8`"
              :style="{ animationDelay: `${index * 0.1}s` }"
            >
              <!-- Timeline Dot -->
              <div :class="`absolute w-4 h-4 rounded-full border-4 border-white shadow-lg z-10 ${getTimelineDotColor(index)} left-4 md:left-1/2 transform md:-translate-x-1/2`"></div>
              
              <!-- Content Card -->
              <div :class="`modern-card p-4 md:p-6 hover:shadow-xl transition-all duration-300 ${index % 2 === 0 ? 'md:mr-8 md:w-5/12 md:ml-auto' : 'md:ml-8 md:w-5/12'} w-full ${getCardBorderColor(index)}`">
                <div class="mb-4">
                  <div :class="`text-sm font-medium mb-2 ${getDateColor(index)}`">
                    {{ formatDate(exp.start_date) }} - {{ exp.is_current ? 'Devam Ediyor' : formatDate(exp.end_date) }}
                  </div>
                  <h3 class="text-xl font-bold text-gray-900 mb-2">{{ exp.position }}</h3>
                  <div class="flex items-center space-x-2 mb-3 flex-wrap">
                    <h4 :class="`text-lg font-semibold ${getCompanyColor(index)}`">{{ exp.company_name }}</h4>
                    <span v-if="exp.location" class="text-gray-500">• {{ exp.location }}</span>
                    <span v-if="exp.work_type" :class="`px-2 py-1 rounded-full text-xs font-medium ${getWorkTypeBadgeColor(index)}`">
                      {{ getWorkTypeLabel(exp.work_type) }}
                    </span>
                  </div>
                </div>
                
                <!-- Short Description -->
                <div v-if="exp.description" class="mb-4">
                  <p class="text-gray-700 leading-relaxed line-clamp-3">{{ exp.description }}</p>
                  <button
                    v-if="exp.description && exp.description.length > 150"
                    @click="selectedExperience = exp"
                    :class="`text-sm font-medium mt-2 transition-colors ${getReadMoreColor(index)}`"
                  >
                    Devamını oku →
                  </button>
                </div>
                
                <div v-if="exp.technologies && exp.technologies.length > 0" class="flex flex-wrap gap-2">
                  <span
                    v-for="tech in exp.technologies"
                    :key="tech"
                    :class="`px-3 py-1 rounded-full text-sm font-medium ${getTechBadgeColor(index)}`"
                  >
                    {{ tech }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Education Section -->
      <div v-if="education.length > 0" class="mb-20">
        <div class="text-center mb-12 animate-fade-up">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Eğitim
          </h2>
          <p class="text-lg text-gray-600">
            Akademik geçmişim
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div
            v-for="(edu, index) in sortedEducation"
            :key="edu.id"
            class="modern-card p-6 hover:shadow-xl transition-all duration-300 animate-fade-up"
            :style="{ animationDelay: `${index * 0.1}s` }"
          >
            <div class="flex items-start space-x-4">
              <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
                <span class="text-white text-lg">🎓</span>
              </div>
              
              <div class="flex-1">
                <div class="text-sm font-medium text-purple-600 mb-1">
                  {{ edu.start_year }} - {{ edu.is_current ? 'Devam Ediyor' : edu.end_year }}
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ edu.degree }}</h3>
                <h4 class="text-md font-semibold text-purple-600 mb-2">{{ edu.institution_name }}</h4>
                <p v-if="edu.field_of_study" class="text-gray-600 text-sm">{{ edu.field_of_study }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Experience Detail Modal -->
      <div v-if="selectedExperience" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click="selectedExperience = null">
        <div
          class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl animate-scale-up"
          @click.stop
        >
          <!-- Modal Header -->
          <div class="relative p-8 pb-6 border-b border-gray-200">
            <button
              @click="selectedExperience = null"
              class="absolute top-4 right-4 w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors"
            >
              <span class="text-gray-600">✕</span>
            </button>
            
            <div class="pr-8">
              <h2 class="text-2xl font-bold text-gray-900 mb-2">
                {{ selectedExperience.position }}
              </h2>
              <h3 class="text-xl font-semibold text-blue-600 mb-2">
                {{ selectedExperience.company_name }}
              </h3>
              <div class="flex items-center space-x-2 text-gray-600">
                <span>{{ formatDate(selectedExperience.start_date) }} - {{ selectedExperience.is_current ? 'Devam Ediyor' : formatDate(selectedExperience.end_date) }}</span>
                <span v-if="selectedExperience.location" class="text-gray-400">•</span>
                <span v-if="selectedExperience.location">{{ selectedExperience.location }}</span>
                <span v-if="selectedExperience.work_type" class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                  {{ getWorkTypeLabel(selectedExperience.work_type) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Modal Content -->
          <div class="p-8">
            <!-- Description -->
            <div class="mb-6">
              <h4 class="text-lg font-semibold text-gray-900 mb-3">Açıklama</h4>
              <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                {{ selectedExperience.description }}
              </p>
            </div>

            <!-- Technologies -->
            <div v-if="selectedExperience.technologies && selectedExperience.technologies.length > 0" class="mb-6">
              <h4 class="text-lg font-semibold text-gray-900 mb-3">Kullanılan Teknolojiler</h4>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="tech in selectedExperience.technologies"
                  :key="tech"
                  class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium"
                >
                  {{ tech }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Skill Detail Modal -->
      <div v-if="selectedSkill" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click="selectedSkill = null">
        <div
          class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl animate-scale-up"
          @click.stop
        >
          <!-- Modal Header -->
          <div class="relative p-8 pb-6">
            <button
              @click="selectedSkill = null"
              class="absolute top-4 right-4 w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors"
            >
              <span class="text-gray-600">✕</span>
            </button>
            
            <div class="flex items-start space-x-6">
              <div :class="`w-20 h-20 rounded-2xl bg-gradient-to-r ${selectedSkill.color} p-4 flex items-center justify-center`">
                <span class="text-3xl">{{ getSkillIcon(selectedSkill.icon) }}</span>
              </div>
              
              <div class="flex-1">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">
                  {{ selectedSkill.title }}
                </h2>
                
                <!-- Proficiency Rating -->
                <div class="flex items-center space-x-3 mb-4">
                  <span class="text-sm font-medium text-gray-600">Yeterlilik:</span>
                  <div class="flex items-center space-x-1">
                    <div
                      v-for="i in 5"
                      :key="i"
                      :class="`w-3 h-3 rounded-full ${
                        i <= selectedSkill.proficiency_level ? 'bg-yellow-400' : 'bg-gray-300'
                      }`"
                    />
                    <span class="text-sm text-gray-600 ml-2">
                      {{ selectedSkill.proficiency_level }}/5
                    </span>
                  </div>
                </div>
                
                <!-- Proficiency Level Text -->
                <div class="mb-4">
                  <span :class="`inline-block px-3 py-1 rounded-full text-sm font-medium ${
                    selectedSkill.proficiency_level === 5 ? 'bg-green-100 text-green-800' :
                    selectedSkill.proficiency_level === 4 ? 'bg-blue-100 text-blue-800' :
                    selectedSkill.proficiency_level === 3 ? 'bg-yellow-100 text-yellow-800' :
                    selectedSkill.proficiency_level === 2 ? 'bg-orange-100 text-orange-800' :
                    'bg-red-100 text-red-800'
                  }`">
                    {{ getProficiencyLabel(selectedSkill.proficiency_level) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Content -->
          <div class="px-8 pb-8">
            <!-- Description -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                <span class="mr-2">📝</span>
                Açıklama
              </h3>
              <p class="text-gray-700 leading-relaxed">
                {{ selectedSkill.description }}
              </p>
            </div>

            <!-- Technologies -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="mr-2">🛠️</span>
                Teknolojiler & Araçlar
                <span class="ml-2 text-sm text-gray-500">
                  ({{ (Array.isArray(selectedSkill.technologies) ? selectedSkill.technologies.length : 0) }} adet)
                </span>
              </h3>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div
                  v-for="(tech, index) in (Array.isArray(selectedSkill.technologies) ? selectedSkill.technologies : [])"
                  :key="tech"
                  class="bg-gradient-to-r from-gray-50 to-gray-100 hover:from-blue-50 hover:to-purple-50 border border-gray-200 hover:border-blue-300 rounded-lg p-3 text-center transition-all duration-300 transform hover:scale-105 animate-fade-up"
                  :style="{ animationDelay: `${index * 0.05}s` }"
                >
                  <span class="text-sm font-medium text-gray-700 hover:text-blue-700">
                    {{ tech }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Experience Level Details -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 border border-blue-200">
              <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                <span class="mr-2">💼</span>
                Deneyim Detayları
              </h3>
              
              <div class="space-y-3">
                <div v-if="selectedSkill.proficiency_level >= 4" class="flex items-center space-x-2">
                  <span class="text-green-500">✅</span>
                  <span class="text-sm text-gray-700">Production ortamında aktif kullanım</span>
                </div>
                <div v-if="selectedSkill.proficiency_level >= 3" class="flex items-center space-x-2">
                  <span class="text-green-500">✅</span>
                  <span class="text-sm text-gray-700">Karmaşık problemleri çözebilme</span>
                </div>
                <div v-if="selectedSkill.proficiency_level >= 2" class="flex items-center space-x-2">
                  <span class="text-green-500">✅</span>
                  <span class="text-sm text-gray-700">Temel implementasyonlar</span>
                </div>
                <div v-if="selectedSkill.proficiency_level >= 5" class="flex items-center space-x-2">
                  <span class="text-blue-500">🏆</span>
                  <span class="text-sm text-gray-700">Mentoring ve team lead deneyimi</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { MapPin, Phone, Mail, Github, Linkedin, Calendar, Users, Briefcase } from 'lucide-vue-next';
import { usePersonalInfo } from '../composables/usePersonalInfo';
import axios from 'axios';

const loading = ref(true);
const selectedSkill = ref(null);
const selectedExperience = ref(null);
const experiences = ref([]);
const education = ref([]);
const { fetchPersonalInfo, getInfo, getAboutInfo, getContactInfo, getSocialLinks, getFileUrls, getSkills } = usePersonalInfo();

const aboutData = computed(() => {
  const about = getAboutInfo();
  const contact = getContactInfo();
  const social = getSocialLinks();
  const files = getFileUrls();
  
  return {
    name: about.fullName || 'Tunahan Akduhan',
    title: about.title || 'Full Stack Developer & Backend Specialist',
    bio: about.bio || 'Modern web teknolojileri ile kullanıcı deneyimi odaklı projeler geliştiriyorum.',
    location: about.location || contact.location || getInfo('address') || getInfo('location') || 'Bursa, Türkiye',
    phone: contact.phone || '+90 (555) 123-4567',
    email: contact.email || 'akduhancontact@gmail.com',
    githubUrl: social.github || 'https://github.com/tunahanakduhan',
    linkedinUrl: social.linkedin || 'https://linkedin.com/in/tunahanakduhan'
  };
});

const skills = ref([]);

const fetchSkills = async () => {
  try {
    const response = await axios.get('/api/skills');
    skills.value = response.data.map(skill => ({
      ...skill,
      technologies: typeof skill.technologies === 'string' 
        ? JSON.parse(skill.technologies) 
        : (Array.isArray(skill.technologies) ? skill.technologies : [])
    }));
  } catch (error) {
    console.error('Skills yüklenemedi:', error);
    // Fallback skills
    skills.value = [
      {
        id: '1',
        title: 'Backend Development',
        description: 'Laravel, PHP, Node.js ile güvenli ve ölçeklenebilir backend sistemler geliştiriyorum.',
        icon: 'Server',
        color: 'from-blue-500 to-blue-600',
        technologies: ['Laravel', 'PHP', 'Node.js', 'Express.js', 'MySQL', 'PostgreSQL', 'Redis'],
        proficiency_level: 5,
        order: 1
      },
      {
        id: '2',
        title: 'API Development',
        description: 'RESTful API tasarımı, entegrasyonu ve dokümantasyonu konularında uzmanlığım var.',
        icon: 'Code',
        color: 'from-green-500 to-green-600',
        technologies: ['REST API', 'GraphQL', 'Sanctum', 'Postman', 'Swagger', 'API Security'],
        proficiency_level: 5,
        order: 2
      },
      {
        id: '3',
        title: 'Database Management',
        description: 'Veri modelleme, optimizasyon ve yönetimi konularında deneyimliyim.',
        icon: 'Database',
        color: 'from-purple-500 to-purple-600',
        technologies: ['MySQL', 'PostgreSQL', 'Eloquent ORM', 'Redis', 'MongoDB'],
        proficiency_level: 4,
        order: 3
      },
      {
        id: '4',
        title: 'Frontend Development',
        description: 'Modern JavaScript framework\'leri ile kullanıcı dostu arayüzler geliştiriyorum.',
        icon: 'Globe',
        color: 'from-orange-500 to-orange-600',
        technologies: ['React', 'Vue.js', 'Next.js', 'TypeScript', 'Tailwind CSS'],
        proficiency_level: 4,
        order: 4
      },
      {
        id: '5',
        title: 'DevOps & Deployment',
        description: 'CI/CD pipeline\'ları ve cloud teknolojileri ile deployment süreçlerini yönetiyorum.',
        icon: 'Cloud',
        color: 'from-indigo-500 to-indigo-600',
        technologies: ['Docker', 'AWS', 'CI/CD', 'Linux', 'Nginx', 'Apache'],
        proficiency_level: 4,
        order: 5
      },
      {
        id: '6',
        title: 'Search & Analytics',
        description: 'Elasticsearch ve analitik sistemlerin tasarımı ve optimizasyonu.',
        icon: 'Search',
        color: 'from-red-500 to-red-600',
        technologies: ['Elasticsearch', 'Laravel Scout', 'Algolia', 'Analytics'],
        proficiency_level: 4,
        order: 6
      }
    ];
  }
};

const socialLinks = computed(() => {
  return [
    { icon: Github, url: aboutData.value.githubUrl, label: 'GitHub' },
    { icon: Linkedin, url: aboutData.value.linkedinUrl, label: 'LinkedIn' },
  ].filter(link => link.url);
});

const hasStatistics = computed(() => {
  return true; // Başlangıç yılı her zaman gösterilsin
});

const bioParagraphs = computed(() => {
  const aboutInfo = getAboutInfo();
  const aboutSummary = aboutInfo.aboutSummary || 'Backend geliştirme alanında uzmanlaşmış, modern web teknolojileri ile ölçeklenebilir çözümler üreten bir yazılım geliştiricisiyim. Laravel, Node.js ve RESTful API teknolojileri konusunda deneyimli, güvenli ve performanslı sistemler geliştiriyorum.';
  return aboutSummary.split('\n').filter(p => p.trim());
});

const sortedExperiences = computed(() => {
  return [...experiences.value].sort((a, b) => {
    // Önce current olanları en üste
    if (a.is_current && !b.is_current) return -1;
    if (!a.is_current && b.is_current) return 1;
    
    // Sonra start_date'e göre en yeniden eskiye
    const dateA = new Date(a.start_date);
    const dateB = new Date(b.start_date);
    return dateB - dateA;
  });
});

const sortedEducation = computed(() => {
  return [...education.value].sort((a, b) => {
    // Önce current olanları en üste
    if (a.is_current && !b.is_current) return -1;
    if (!a.is_current && b.is_current) return 1;
    
    // Sonra start_year'a göre en yeniden eskiye
    return (b.start_year || 0) - (a.start_year || 0);
  });
});

const getSkillIcon = (iconName) => {
  const icons = {
    Database: '🗄️',
    Code: '💻',
    Smartphone: '📱',
    Globe: '🌐',
    Search: '🔍',
    Server: '🖥️',
    Cloud: '☁️',
    Shield: '🛡️',
    Zap: '⚡',
    Tool: '🔧',
    Brain: '🧠',
    Settings: '⚙️'
  };
  return icons[iconName] || '💻';
};

const getProficiencyLabel = (level) => {
  const labels = {
    5: '⭐ Uzman',
    4: '🚀 İleri Seviye',
    3: '📈 Orta Seviye',
    2: '📚 Temel Seviye',
    1: '🌱 Başlangıç'
  };
  return labels[level] || 'Bilinmiyor';
};

const fetchExperiences = async () => {
  try {
    const response = await axios.get('/api/experiences');
    experiences.value = response.data;
  } catch (error) {
    console.error('Deneyim verileri alınamadı:', error);
  }
};

const fetchEducation = async () => {
  try {
    const response = await axios.get('/api/education');
    education.value = response.data;
  } catch (error) {
    console.error('Eğitim verileri alınamadı:', error);
  }
};

const formatDate = (date) => {
  if (!date) return '';
  const d = new Date(date);
  return d.toLocaleDateString('tr-TR', { year: 'numeric', month: 'long' });
};

const getWorkTypeLabel = (workType) => {
  const labels = {
    office: 'Ofiste',
    remote: 'Remote',
    hybrid: 'Hibrit'
  };
  return labels[workType] || workType;
};

// Timeline renk fonksiyonları - Mavi tonları
const getTimelineDotColor = (index) => {
  const colors = [
    'bg-blue-500',
    'bg-blue-600', 
    'bg-indigo-500',
    'bg-sky-500',
    'bg-cyan-500',
    'bg-slate-500'
  ];
  return colors[index % colors.length];
};

const getCardBorderColor = (index) => {
  const colors = [
    'border-l-4 border-l-blue-500',
    'border-l-4 border-l-blue-600',
    'border-l-4 border-l-indigo-500', 
    'border-l-4 border-l-sky-500',
    'border-l-4 border-l-cyan-500',
    'border-l-4 border-l-slate-500'
  ];
  return colors[index % colors.length];
};

const getDateColor = (index) => {
  const colors = [
    'text-blue-600',
    'text-blue-700',
    'text-indigo-600',
    'text-sky-600', 
    'text-cyan-600',
    'text-slate-600'
  ];
  return colors[index % colors.length];
};

const getCompanyColor = (index) => {
  const colors = [
    'text-blue-700',
    'text-blue-800',
    'text-indigo-700',
    'text-sky-700',
    'text-cyan-700', 
    'text-slate-700'
  ];
  return colors[index % colors.length];
};

const getWorkTypeBadgeColor = (index) => {
  const colors = [
    'bg-blue-100 text-blue-800',
    'bg-blue-50 text-blue-700',
    'bg-indigo-100 text-indigo-800',
    'bg-sky-100 text-sky-800',
    'bg-cyan-100 text-cyan-800',
    'bg-slate-100 text-slate-800'
  ];
  return colors[index % colors.length];
};

const getReadMoreColor = (index) => {
  const colors = [
    'text-blue-600 hover:text-blue-800',
    'text-blue-700 hover:text-blue-900', 
    'text-indigo-600 hover:text-indigo-800',
    'text-sky-600 hover:text-sky-800',
    'text-cyan-600 hover:text-cyan-800',
    'text-slate-600 hover:text-slate-800'
  ];
  return colors[index % colors.length];
};

const getTechBadgeColor = (index) => {
  const colors = [
    'bg-blue-50 text-blue-700 border border-blue-200',
    'bg-indigo-50 text-indigo-700 border border-indigo-200',
    'bg-sky-50 text-sky-700 border border-sky-200', 
    'bg-cyan-50 text-cyan-700 border border-cyan-200',
    'bg-slate-50 text-slate-700 border border-slate-200',
    'bg-blue-100 text-blue-800 border border-blue-300'
  ];
  return colors[index % colors.length];
};

const fetchAboutData = async () => {
  try {
    // Fetch personal info and skills
    await fetchPersonalInfo();
    // Fetch CV data and skills
    await Promise.all([
      fetchExperiences(),
      fetchEducation(),
      fetchSkills()
    ]);
  } catch (error) {
    console.error('About verileri alınamadı, static data kullanılıyor:', error);
  } finally {
    loading.value = false;
  }
};

// ESC tuşu ile modal kapatma
const handleKeyDown = (event) => {
  if (event.key === 'Escape') {
    if (selectedSkill.value) {
      selectedSkill.value = null;
    }
    if (selectedExperience.value) {
      selectedExperience.value = null;
    }
  }
};

onMounted(() => {
  fetchAboutData();
  // ESC tuşu listener'ı ekle
  document.addEventListener('keydown', handleKeyDown);
});

// Component unmount olurken listener'ı temizle
onUnmounted(() => {
  document.removeEventListener('keydown', handleKeyDown);
});
</script>

<style>
.modern-card {
  @apply bg-white rounded-3xl shadow-lg border border-gray-200/80;
}

.line-clamp-3 {
  @apply overflow-hidden;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
}

/* Responsive timeline adjustments */
@media (max-width: 768px) {
  .timeline-card {
    width: 100% !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    text-align: left !important;
  }
  
  .timeline-card.even {
    margin-left: 0 !important;
    margin-right: 0 !important;
    text-align: left !important;
  }
}

@keyframes fade-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes scale-up {
  from {
    opacity: 0;
    transform: scale(0.8) translateY(50px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-fade-up {
  animation: fade-up 0.6s ease-out forwards;
}

.animate-scale-up {
  animation: scale-up 0.3s ease-out forwards;
}
</style>
