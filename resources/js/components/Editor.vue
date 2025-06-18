<template>
  <div class="border border-gray-300 rounded-lg overflow-hidden bg-white shadow-sm">
    <!-- Toolbar -->
    <div v-if="editor" class="bg-gray-50 border-b border-gray-200 p-3">
      <!-- Text Formatting Row -->
      <div class="flex items-center flex-wrap gap-1 mb-2">
        <div class="flex items-center bg-white rounded border border-gray-200">
          <button type="button" @click="editor.chain().focus().toggleBold().run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive('bold') }" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-200 last:border-r-0" title="Kalın (Ctrl+B)">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/></svg>
          </button>
          <button type="button" @click="editor.chain().focus().toggleItalic().run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive('italic') }" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-200 last:border-r-0" title="İtalik (Ctrl+I)">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg>
          </button>
          <button type="button" @click="editor.chain().focus().toggleUnderline().run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive('underline') }" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-200 last:border-r-0" title="Altı çizili (Ctrl+U)">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 4v7a6 6 0 0 0 12 0V4"/><line x1="4" y1="20" x2="20" y2="20"/></svg>
          </button>
          <button type="button" @click="editor.chain().focus().toggleStrike().run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive('strike') }" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors" title="Üstü çizili">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4H9a3 3 0 0 0-2.83 4"/><path d="M14 12a4 4 0 0 1 0 8H6"/><line x1="4" y1="12" x2="20" y2="12"/></svg>
          </button>
        </div>

        <!-- Text Color -->
        <div class="flex items-center bg-white rounded border border-gray-200">
          <select @change="setTextColor($event.target.value)" class="p-2 text-sm border-0 bg-transparent focus:ring-0" title="Metin Rengi">
            <option value="#000000">Siyah</option>
            <option value="#ef4444">Kırmızı</option>
            <option value="#3b82f6">Mavi</option>
            <option value="#10b981">Yeşil</option>
            <option value="#f59e0b">Turuncu</option>
            <option value="#8b5cf6">Mor</option>
          </select>
        </div>

        <!-- Highlight -->
        <div class="flex items-center bg-white rounded border border-gray-200">
          <button type="button" @click="editor.chain().focus().toggleHighlight().run()" :class="{ 'bg-yellow-100 text-yellow-600': editor.isActive('highlight') }" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors" title="Vurgula">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          </button>
        </div>
      </div>

      <!-- Headings and Structure Row -->
      <div class="flex items-center flex-wrap gap-1 mb-2">
        <div class="flex items-center bg-white rounded border border-gray-200">
          <button type="button" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive('heading', { level: 1 }) }" class="px-3 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-200" title="Başlık 1">
            H1
          </button>
          <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive('heading', { level: 2 }) }" class="px-3 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-200" title="Başlık 2">
            H2
          </button>
          <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive('heading', { level: 3 }) }" class="px-3 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 transition-colors" title="Başlık 3">
            H3
          </button>
        </div>

        <div class="flex items-center bg-white rounded border border-gray-200">
          <button type="button" @click="editor.chain().focus().setParagraph().run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive('paragraph') }" class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 transition-colors" title="Normal metin">
            P
          </button>
        </div>
      </div>

      <!-- Lists and Alignment Row -->
      <div class="flex items-center flex-wrap gap-1 mb-2">
        <div class="flex items-center bg-white rounded border border-gray-200">
          <button type="button" @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive('bulletList') }" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-200" title="Madde listesi">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
          </button>
          <button type="button" @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive('orderedList') }" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors" title="Numaralı liste">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>
          </button>
        </div>

        <div class="flex items-center bg-white rounded border border-gray-200">
          <button type="button" @click="editor.chain().focus().setTextAlign('left').run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive({ textAlign: 'left' }) }" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-200" title="Sola hizala">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/></svg>
          </button>
          <button type="button" @click="editor.chain().focus().setTextAlign('center').run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive({ textAlign: 'center' }) }" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-200" title="Ortala">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="10" x2="6" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="18" y1="18" x2="6" y2="18"/></svg>
          </button>
          <button type="button" @click="editor.chain().focus().setTextAlign('right').run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive({ textAlign: 'right' }) }" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors" title="Sağa hizala">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="7" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="7" y2="18"/></svg>
          </button>
        </div>
      </div>

      <!-- Advanced Features Row -->
      <div class="flex items-center flex-wrap gap-1">
        <div class="flex items-center bg-white rounded border border-gray-200">
          <button type="button" @click="editor.chain().focus().toggleBlockquote().run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive('blockquote') }" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-200" title="Alıntı">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/></svg>
          </button>
          <button type="button" @click="editor.chain().focus().toggleCodeBlock().run()" :class="{ 'bg-blue-100 text-blue-600': editor.isActive('codeBlock') }" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-200" title="Kod bloğu">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16,18 22,12 16,6"/><polyline points="8,6 2,12 8,18"/></svg>
          </button>
          <button type="button" @click="insertLink" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-200" title="Link ekle">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.72"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.72-1.72"/></svg>
          </button>
          <button type="button" @click="insertImage" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors" title="Resim ekle">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="M21 15l-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
          </button>
        </div>

        <div class="flex items-center bg-white rounded border border-gray-200">
          <button type="button" @click="editor.chain().focus().setHorizontalRule().run()" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors border-r border-gray-200" title="Yatay çizgi">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/></svg>
          </button>
          <button type="button" @click="editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors" title="Tablo ekle">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/></svg>
          </button>
        </div>

        <div class="flex items-center bg-white rounded border border-gray-200">
          <button type="button" @click="editor.chain().focus().undo().run()" :disabled="!editor.can().undo()" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed border-r border-gray-200" title="Geri al (Ctrl+Z)">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/></svg>
          </button>
          <button type="button" @click="editor.chain().focus().redo().run()" :disabled="!editor.can().redo()" class="p-2 text-gray-600 hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" title="İleri al (Ctrl+Y)">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3l3 2.7"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Editor Content -->
    <div class="relative">
      <editor-content 
        :editor="editor" 
        class="prose prose-lg max-w-none p-6 min-h-[400px] focus:outline-none" 
        placeholder="Makale içeriğinizi buraya yazın..."
      />
      
      <!-- Word Count -->
      <div class="absolute bottom-3 right-3 text-xs text-gray-400 bg-white px-2 py-1 rounded border">
        {{ wordCount }} kelime | {{ characterCount }} karakter
      </div>
    </div>
  </div>
</template>

<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'
import TextAlign from '@tiptap/extension-text-align'
import Highlight from '@tiptap/extension-highlight'
import Link from '@tiptap/extension-link'
import Image from '@tiptap/extension-image'
import Table from '@tiptap/extension-table'
import TableRow from '@tiptap/extension-table-row'
import TableHeader from '@tiptap/extension-table-header'
import TableCell from '@tiptap/extension-table-cell'
import Color from '@tiptap/extension-color'
import TextStyle from '@tiptap/extension-text-style'
import { watch, computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit.configure({
      heading: {
        levels: [1, 2, 3],
      },
      codeBlock: {
        languageClassPrefix: 'language-',
      },
    }),
    Underline,
    TextAlign.configure({
      types: ['heading', 'paragraph'],
    }),
    Highlight.configure({
      multicolor: true
    }),
    Link.configure({
      openOnClick: false,
      HTMLAttributes: {
        class: 'text-blue-600 underline hover:text-blue-800',
      },
    }),
    Image.configure({
      HTMLAttributes: {
        class: 'max-w-full h-auto rounded-lg shadow-sm',
      },
    }),
    Table.configure({
      resizable: true,
    }),
    TableRow,
    TableHeader,
    TableCell,
    TextStyle,
    Color,
  ],
  onUpdate: () => {
    emit('update:modelValue', editor.value.getHTML())
  },
  editorProps: {
    attributes: {
      class: 'prose prose-lg max-w-none focus:outline-none editor-content',
      'data-placeholder': 'Makale içeriğinizi buraya yazın...',
    },
  },
})

const wordCount = computed(() => {
  if (!editor.value) return 0
  const text = editor.value.getText()
  return text.trim() ? text.trim().split(/\s+/).length : 0
})

const characterCount = computed(() => {
  if (!editor.value) return 0
  return editor.value.getText().length
})

const setTextColor = (color) => {
  editor.value.chain().focus().setColor(color).run()
}

const insertLink = () => {
  const url = window.prompt('Link URL:')
  if (url) {
    const text = window.prompt('Link metni:', url)
    if (text) {
      editor.value.chain().focus().insertContent(`<a href="${url}">${text}</a>`).run()
    }
  }
}

const insertImage = () => {
  const url = window.prompt('Resim URL:')
  if (url) {
    const alt = window.prompt('Resim açıklaması (opsiyonel):', '')
    editor.value.chain().focus().setImage({ src: url, alt: alt || '' }).run()
  }
}

watch(() => props.modelValue, (value) => {
  if (!editor.value) return
  const isSame = editor.value.getHTML() === value
  if (isSame) {
    return
  }
  editor.value.commands.setContent(value, false)
})

</script>

<style>
.prose {
  max-width: none;
}

.editor-content .tiptap {
  outline: none;
}

.editor-content .tiptap p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left;
  color: #9ca3af;
  pointer-events: none;
  height: 0;
  font-style: italic;
}

.editor-content .tiptap h1 {
  @apply text-3xl font-bold mb-4 mt-6;
}

.editor-content .tiptap h2 {
  @apply text-2xl font-bold mb-3 mt-5;
}

.editor-content .tiptap h3 {
  @apply text-xl font-bold mb-2 mt-4;
}

.editor-content .tiptap blockquote {
  @apply border-l-4 border-gray-300 pl-4 py-2 my-4 italic bg-gray-50;
}

.editor-content .tiptap pre {
  @apply bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto;
}

.editor-content .tiptap code {
  @apply bg-gray-100 text-gray-800 px-1 py-0.5 rounded text-sm;
}

.editor-content .tiptap ul, .editor-content .tiptap ol {
  @apply my-4 ml-6;
}

.editor-content .tiptap li {
  @apply mb-1;
}

.editor-content .tiptap table {
  @apply border-collapse border border-gray-300 my-4 w-full;
}

.editor-content .tiptap th, .editor-content .tiptap td {
  @apply border border-gray-300 px-3 py-2;
}

.editor-content .tiptap th {
  @apply bg-gray-100 font-semibold;
}

.editor-content .tiptap hr {
  @apply my-6 border-t-2 border-gray-200;
}

.editor-content .tiptap img {
  @apply my-4 rounded-lg shadow-sm;
}

.editor-content .tiptap mark {
  @apply bg-yellow-200 px-1 rounded;
}

/* Table selection styles */
.editor-content .tiptap .selectedCell {
  @apply bg-blue-100;
}

/* Link styles */
.editor-content .tiptap a {
  @apply text-blue-600 underline hover:text-blue-800 transition-colors;
}
</style>
