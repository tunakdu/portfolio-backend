import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'

export default function useArticles() {
    const articles = ref([])
    const article = ref({})
    const router = useRouter()
    const errors = ref({})
    const isLoading = ref(false)

    const getArticles = async () => {
        try {
            isLoading.value = true
            let response = await axios.get('/api/articles')
            articles.value = response.data.data
        } catch (e) {
            console.error(e)
            Swal.fire('Hata!', 'Makaleler yüklenirken bir sorun oluştu.', 'error')
        } finally {
            isLoading.value = false
        }
    }

    const getArticle = async (slug) => {
         try {
            isLoading.value = true
            let response = await axios.get(`/api/articles/${slug}`)
            article.value = response.data
        } catch (e) {
             console.error(e)
             if (e.response && e.response.status === 404) {
                 router.push('/admin/articles')
             } else {
                Swal.fire('Hata!', 'Makale yüklenirken bir sorun oluştu.', 'error')
             }
        } finally {
            isLoading.value = false
        }
    }

    const storeArticle = async (data) => {
        errors.value = {}
        try {
            isLoading.value = true
            const response = await axios.post('/api/articles', data)
            await router.push('/admin/articles')
            Swal.fire('Başarılı!', response.data.message || 'Makale başarıyla eklendi.', 'success')
        } catch (e) {
            if (e.response && e.response.status === 422) {
                errors.value = e.response.data.errors
                Swal.fire('Validation Hatası!', 'Lütfen form alanlarını kontrol edin.', 'error')
            } else {
                 console.error(e)
                 Swal.fire('Hata!', 'Makale eklenirken bir hata oluştu.', 'error')
            }
        } finally {
            isLoading.value = false
        }
    }

    const updateArticle = async (slug, data) => {
        errors.value = {}
        try {
            isLoading.value = true
            const response = await axios.put(`/api/articles/${slug}`, data)
            await router.push('/admin/articles')
            Swal.fire('Başarılı!', response.data.message || 'Makale başarıyla güncellendi.', 'success')
        } catch (e) {
             if (e.response && e.response.status === 422) {
                errors.value = e.response.data.errors
                Swal.fire('Validation Hatası!', 'Lütfen form alanlarını kontrol edin.', 'error')
            } else {
                 console.error(e)
                 Swal.fire('Hata!', 'Makale güncellenirken bir hata oluştu.', 'error')
            }
        } finally {
            isLoading.value = false
        }
    }

    const uploadCoverImage = async (file) => {
        try {
            isLoading.value = true
            const formData = new FormData()
            formData.append('cover_image', file)
            
            const response = await axios.post('/api/articles/upload-cover', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            
            Swal.fire('Başarılı!', response.data.message, 'success')
            return response.data.path
        } catch (e) {
            console.error(e)
            if (e.response && e.response.status === 422) {
                const errorMsg = e.response.data.errors?.cover_image?.[0] || 'Dosya yükleme hatası'
                Swal.fire('Hata!', errorMsg, 'error')
            } else {
                Swal.fire('Hata!', 'Görsel yüklenirken bir hata oluştu.', 'error')
            }
            throw e
        } finally {
            isLoading.value = false
        }
    }

    const destroyArticle = async (slug) => {
        await Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu makaleyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'İptal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    await axios.delete(`/api/articles/${slug}`)
                    await getArticles() // Listeyi yenile
                    Swal.fire('Silindi!', 'Makale başarıyla silindi.', 'success')
                } catch (e) {
                    console.error(e)
                    Swal.fire('Hata!', 'Makale silinirken bir hata oluştu.', 'error')
                }
            }
        })
    }


    return {
        articles,
        article,
        errors,
        isLoading,
        getArticles,
        getArticle,
        storeArticle,
        updateArticle,
        uploadCoverImage,
        destroyArticle
    }
}
