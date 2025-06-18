<template>
  <section class="py-24 sm:py-32 bg-gradient-to-b from-white to-blue-50" id="contact">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <!-- Header -->
      <div class="mx-auto max-w-3xl text-center mb-16">
        <div>
          <span class="inline-block px-4 py-2 rounded-full bg-green-100 text-green-600 text-sm font-medium mb-4">
            💬 İletişim
          </span>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
            Birlikte Çalışalım
          </h2>
          <p class="text-xl text-gray-600 leading-relaxed">
            Projeleriniz için benimle iletişime geçmekten çekinmeyin. Beraber harika şeyler
            yapabiliriz!
          </p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
        <!-- Contact Info Cards -->
        <div v-for="item in contactInfo" :key="item.title" class="modern-card p-6 text-center group hover:scale-105 transition-all duration-300">
          <div :class="`w-16 h-16 rounded-2xl ${item.color} flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300`">
            <component :is="item.icon" class="w-8 h-8" />
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">{{ item.title }}</h3>
          <a :href="item.href" class="text-lg font-medium text-blue-600 hover:text-blue-700 transition-colors block mb-2">
            {{ item.value }}
          </a>
          <p class="text-sm text-gray-600">{{ item.description }}</p>
        </div>
      </div>

      <!-- Contact Form -->
      <div class="modern-card p-8 lg:p-12 max-w-4xl mx-auto">
        <div class="text-center mb-8">
          <h3 class="text-3xl font-bold text-gray-900 mb-4">
            Mesaj Gönder
          </h3>
          <p class="text-gray-600">
            Projeniz hakkında detayları paylaşın, size en kısa sürede dönüş yapayım.
          </p>
        </div>

        <form @submit.prevent="onSubmit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name Input -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Ad Soyad *
              </label>
              <input v-model="form.name" type="text" id="name" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all duration-200 bg-white" placeholder="Adınız Soyadınız" />
              <p v-if="errors.name" class="mt-2 text-sm text-red-600 flex items-center">
                <AlertCircle class="w-4 h-4 mr-1" />
                {{ errors.name }}
              </p>
            </div>

            <!-- Email Input -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email Adresi *
              </label>
              <input v-model="form.email" type="email" id="email" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all duration-200 bg-white" placeholder="email@example.com" />
              <p v-if="errors.email" class="mt-2 text-sm text-red-600 flex items-center">
                <AlertCircle class="w-4 h-4 mr-1" />
                {{ errors.email }}
              </p>
            </div>
          </div>

          <!-- Message Input -->
          <div>
            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
              Mesajınız *
            </label>
            <textarea v-model="form.message" id="message" rows="6" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all duration-200 bg-white resize-none" placeholder="Projenizden bahsedin..."></textarea>
            <p v-if="errors.message" class="mt-2 text-sm text-red-600 flex items-center">
              <AlertCircle class="w-4 h-4 mr-1" />
              {{ errors.message }}
            </p>
          </div>

          <div class="text-center">
             <button type="submit" :disabled="isSubmitting" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-xl hover:bg-blue-700 transition-all duration-300 disabled:opacity-50 flex items-center justify-center w-full md:w-auto mx-auto">
                <span v-if="isSubmitting">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Gönderiliyor...
                </span>
                <span v-else class="flex items-center">
                    <Send class="w-5 h-5 mr-2" />
                    Mesajı Gönder
                </span>
            </button>
          </div>
        </form>
         <div v-if="submitStatus === 'success'" class="mt-6 text-center p-4 rounded-lg bg-green-100 text-green-700 flex items-center justify-center">
            <CheckCircle class="w-5 h-5 mr-2" />
            Mesajınız başarıyla gönderildi!
        </div>
         <div v-if="submitStatus === 'error'" class="mt-6 text-center p-4 rounded-lg bg-red-100 text-red-700 flex items-center justify-center">
            <AlertCircle class="w-5 h-5 mr-2" />
            Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, shallowRef } from 'vue';
import { Mail, Phone, MapPin, Send, CheckCircle, AlertCircle } from 'lucide-vue-next';
import axios from 'axios';

const settings = ref(null);
const loading = ref(false); // Başlangıçta yüklenmiyor
const isSubmitting = ref(false);
const submitStatus = ref('idle'); // 'idle', 'success', 'error'

const form = ref({
  name: '',
  email: '',
  message: '',
});

const errors = ref({});

const contactInfo = ref([]);

// API çağrılarını statik veriyle değiştiriyoruz.
// Gerçek bir projede bu verileri API'den alabilirsiniz.
const setupPageData = () => {
    const displayEmail = "akduhant@gmail.com";
    const displayPhone = "+90 542 740 19 96";
    const displayLocation = "Bursa, Türkiye";

    contactInfo.value = [
        {
            icon: shallowRef(Mail),
            title: "Email",
            value: displayEmail,
            href: `mailto:${displayEmail}`,
            description: "Mesajlarınızı 24 saat içinde yanıtlıyorum",
            color: "bg-blue-100 text-blue-600"
        },
        {
            icon: shallowRef(Phone),
            title: "Telefon",
            value: displayPhone,
            href: `tel:${displayPhone.replace(/\s/g, '')}`,
            description: "Haftaiçi 09:00 - 18:00 arasında ulaşabilirsiniz",
            color: "bg-green-100 text-green-600"
        },
        {
            icon: shallowRef(MapPin),
            title: "Konum",
            value: displayLocation,
            href: "#",
            description: "Uzaktan çalışma ve ofis toplantıları mümkün",
            color: "bg-purple-100 text-purple-600"
        }
    ];
};

onMounted(() => {
    setupPageData();
});

const validate = () => {
  errors.value = {};
  if (form.value.name.length < 2) {
    errors.value.name = 'İsim en az 2 karakter olmalıdır';
  }
  if (!/^\S+@\S+\.\S+$/.test(form.value.email)) {
    errors.value.email = 'Geçerli bir email adresi giriniz';
  }
  if (form.value.message.length < 10) {
    errors.value.message = 'Mesaj en az 10 karakter olmalıdır';
  }
  return Object.keys(errors.value).length === 0;
};

const onSubmit = async () => {
  if (!validate()) {
    return;
  }

  isSubmitting.value = true;
  submitStatus.value = 'idle';

  try {
    // Laravel API endpoint'i: /api/contact
    await axios.post('/api/contact', form.value);

    submitStatus.value = 'success';
    form.value = { name: '', email: '', message: '' }; // Formu sıfırla
    errors.value = {}; // Hataları temizle
  } catch (err) {
    submitStatus.value = 'error';
    console.error('Mesaj gönderme hatası:', err);
  } finally {
    isSubmitting.value = false;
    setTimeout(() => {
      submitStatus.value = 'idle';
    }, 5000); // 5 saniye sonra durumu sıfırla
  }
};
</script>

<style scoped>
.modern-card {
    background-color: white;
    border-radius: 1.5rem; /* 24px */
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(229, 231, 235, 0.5);
    transition: all 0.3s ease-in-out;
}
</style>
