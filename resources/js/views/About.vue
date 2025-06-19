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
          
          <!-- Profile Image -->
          <div v-if="aboutData.profileImage" class="w-48 h-48 mx-auto mb-8 relative">
            <img
              :src="aboutData.profileImage"
              :alt="aboutData.name"
              class="w-full h-full rounded-full object-cover border-4 border-white shadow-xl"
            />
          </div>

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

          <!-- CV Download -->
          <div v-if="aboutData.cvUrl" class="mb-8">
            <a
              :href="aboutData.cvUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-300"
            >
              <Download class="w-4 h-4 mr-2" />
              CV İndir
            </a>
          </div>

          <!-- Bio -->
          <div class="prose prose-lg mx-auto text-gray-600 text-left">
            <p
              v-for="(paragraph, index) in bioParagraphs"
              :key="index"
              class="mb-4 leading-relaxed animate-fade-up"
              :style="{ animationDelay: `${index * 0.1}s` }"
            >
              {{ paragraph }}
            </p>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div v-if="hasStatistics" class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20 animate-fade-up">
        <div v-if="aboutData.yearsExperience" class="text-center">
          <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Calendar class="w-8 h-8 text-blue-600" />
          </div>
          <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ aboutData.yearsExperience }}+</h3>
          <p class="text-gray-600">Yıl Deneyim</p>
        </div>
        <div v-if="aboutData.projectsCount" class="text-center">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Briefcase class="w-8 h-8 text-green-600" />
          </div>
          <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ aboutData.projectsCount }}+</h3>
          <p class="text-gray-600">Tamamlanan Proje</p>
        </div>
        <div v-if="aboutData.happyClients" class="text-center">
          <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Users class="w-8 h-8 text-purple-600" />
          </div>
          <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ aboutData.happyClients }}+</h3>
          <p class="text-gray-600">Mutlu Müşteri</p>
        </div>
      </div>

      <!-- Skills Grid -->
      <div v-if="skills.length > 0" class="mb-20">
        <div class="text-center mb-12 animate-fade-up">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Yeteneklerim
          </h2>
          <p class="text-lg text-gray-600">
            Uzmanlaştığım teknolojiler ve alanlar
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(skill, index) in skills"
            :key="skill.id"
            class="modern-card p-6 group hover:scale-105 transition-all duration-300 cursor-pointer h-80 flex flex-col animate-fade-up"
            :style="{ animationDelay: `${index * 0.1}s` }"
            @click="selectedSkill = skill"
          >
            <!-- Icon -->
            <div :class="`w-14 h-14 rounded-xl bg-gradient-to-r ${skill.color} p-3 mb-4 group-hover:scale-110 transition-transform duration-300`">
              <div class="w-full h-full bg-white rounded-lg flex items-center justify-center">
                <span class="text-xl">{{ getSkillIcon(skill.icon) }}</span>
              </div>
            </div>

            <!-- Title & Rating -->
            <div class="flex justify-between items-start mb-3">
              <h3 class="text-lg font-bold text-gray-900 leading-tight">
                {{ skill.title }}
              </h3>
              <div class="flex items-center space-x-1 ml-2">
                <div
                  v-for="i in 5"
                  :key="i"
                  :class="`w-2 h-2 rounded-full ${
                    i <= skill.proficiency_level ? 'bg-yellow-400' : 'bg-gray-300'
                  }`"
                />
              </div>
            </div>

            <!-- Description -->
            <p class="text-gray-600 text-sm mb-4 leading-relaxed flex-1 line-clamp-3">
              {{ skill.description }}
            </p>

            <!-- Technologies Preview -->
            <div class="flex flex-wrap gap-1 mb-4">
              <span
                v-for="tech in skill.technologies.slice(0, 3)"
                :key="tech"
                class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium"
              >
                {{ tech }}
              </span>
              <span
                v-if="skill.technologies.length > 3"
                class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium"
              >
                +{{ skill.technologies.length - 3 }} daha
              </span>
            </div>

            <!-- View Details Button -->
            <button class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-2 px-4 rounded-lg font-medium text-sm hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform group-hover:scale-105">
              <span class="flex items-center justify-center space-x-2">
                <span>🚀</span>
                <span>Detayları Gör</span>
              </span>
            </button>
          </div>
        </div>
      </div>

      <!-- Professional Approach Section -->
      <div class="modern-card p-8 md:p-12 bg-gradient-to-r from-blue-50 to-purple-50 border-none animate-fade-up">
        <div class="max-w-4xl mx-auto text-center">
          <div class="mb-6">
            <span class="text-4xl mb-4 block">🚀</span>
            <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Profesyonel Yaklaşım</h3>
          </div>
          <p class="text-lg md:text-xl text-gray-700 leading-relaxed mb-8">
            Her projede <span class="text-blue-600 font-semibold">code quality</span>,
            <span class="text-purple-600 font-semibold"> performance</span> ve
            <span class="text-green-600 font-semibold"> user experience</span> üçlüsüne odaklanarak,
            modern web standartlarına uygun, ölçeklenebilir ve maintainable çözümler sunuyorum.
          </p>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="text-center">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3"><span class="text-blue-600 text-xl">⚡</span></div>
              <h4 class="font-semibold text-gray-900 mb-2">Performance</h4>
              <p class="text-gray-600 text-sm">Hızlı ve optimize edilmiş uygulamalar</p>
            </div>
            <div class="text-center">
              <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3"><span class="text-purple-600 text-xl">🎨</span></div>
              <h4 class="font-semibold text-gray-900 mb-2">Modern UI/UX</h4>
              <p class="text-gray-600 text-sm">Kullanıcı dostu arayüz tasarımları</p>
            </div>
            <div class="text-center">
              <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3"><span class="text-green-600 text-xl">🔧</span></div>
              <h4 class="font-semibold text-gray-900 mb-2">Clean Code</h4>
              <p class="text-gray-600 text-sm">Sürdürülebilir ve temiz kod yapısı</p>
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
                  ({{ selectedSkill.technologies.length }} adet)
                </span>
              </h3>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div
                  v-for="(tech, index) in selectedSkill.technologies"
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
import { MapPin, Phone, Mail, Download, Github, Linkedin, Twitter, Instagram, Calendar, Users, Briefcase } from 'lucide-vue-next';
import { usePersonalInfo } from '../composables/usePersonalInfo';
import axios from 'axios';

const loading = ref(true);
const selectedSkill = ref(null);
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
    location: about.location || contact.location || 'İstanbul, Türkiye',
    phone: contact.phone || '+90 (555) 123-4567',
    email: contact.email || 'akduhancontact@gmail.com',
    profileImage: files.profileImage || '',
    cvUrl: files.cv || '',
    githubUrl: social.github || 'https://github.com/tunahanakduhan',
    linkedinUrl: social.linkedin || 'https://linkedin.com/in/tunahanakduhan',
    twitterUrl: social.twitter || '',
    instagramUrl: social.instagram || '',
    yearsExperience: 5,
    projectsCount: 50,
    happyClients: 25
  };
});

const skills = computed(() => {
  const skillsData = getSkills();
  
  const fallbackSkills = [
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
  
  // Database'den gelen skills varsa onları döndür
  if (skillsData && Object.keys(skillsData).length > 0) {
    return Object.entries(skillsData).map(([category, techs], index) => ({
      id: String(index + 1),
      title: category,
      description: `${category} teknolojileri ile profesyonel çözümler geliştiriyorum.`,
      icon: 'Code',
      color: `from-blue-500 to-purple-600`,
      technologies: Array.isArray(techs) ? techs : [],
      proficiency_level: 4,
      order: index + 1
    }));
  }
  
  return fallbackSkills;
});

const socialLinks = computed(() => {
  return [
    { icon: Github, url: aboutData.value.githubUrl, label: 'GitHub' },
    { icon: Linkedin, url: aboutData.value.linkedinUrl, label: 'LinkedIn' },
    { icon: Twitter, url: aboutData.value.twitterUrl, label: 'Twitter' },
    { icon: Instagram, url: aboutData.value.instagramUrl, label: 'Instagram' },
  ].filter(link => link.url);
});

const hasStatistics = computed(() => {
  return aboutData.value.yearsExperience || aboutData.value.projectsCount || aboutData.value.happyClients;
});

const bioParagraphs = computed(() => {
  const bio = aboutData.value.bio || 'Modern web teknolojileri ile kullanıcı deneyimi odaklı projeler geliştiriyorum.';
  return bio.split('\n').filter(p => p.trim());
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

const fetchAboutData = async () => {
  try {
    // Fetch personal info
    await fetchPersonalInfo();
  } catch (error) {
    console.error('About verileri alınamadı, static data kullanılıyor:', error);
  } finally {
    loading.value = false;
  }
};

// ESC tuşu ile modal kapatma
const handleKeyDown = (event) => {
  if (event.key === 'Escape' && selectedSkill.value) {
    selectedSkill.value = null;
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
