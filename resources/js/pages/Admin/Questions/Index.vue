<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { MessageCircle, Send, BookOpen } from 'lucide-vue-next';

interface User {
  id: number;
  name: string;
}

interface Answer {
  id: number;
  answer: string;
  user: User;
  created_at: string;
}

interface Course {
  id: number;
  title: string;
}

interface Section {
  id: number;
  name: string;
  course: Course;
}

interface Lesson {
  id: number;
  name: string;
  section: Section;
}

interface Question {
  id: number;
  question: string;
  user: User;
  lesson: Lesson;
  answers: Answer[];
  created_at: string;
}

interface PaginatedQuestions {
  data: Question[];
  current_page: number;
  last_page: number;
  total: number;
}

interface Props {
  questions: PaginatedQuestions;
}

const props = defineProps<Props>();

const answeringQuestion = ref<number | null>(null);
const answerForms = ref<{ [key: number]: ReturnType<typeof useForm> }>({});

const startAnswering = (questionId: number) => {
  answeringQuestion.value = questionId;
  if (!answerForms.value[questionId]) {
    answerForms.value[questionId] = useForm({
      answer: '',
    });
  }
};

const submitAnswer = (questionId: number) => {
  const form = answerForms.value[questionId];
  if (form) {
    form.post(route('admin.questions.answer', questionId), {
      preserveScroll: true,
      onSuccess: () => {
        answeringQuestion.value = null;
        form.reset();
      },
    });
  }
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<template>
  <Head title="Preguntas de Estudiantes" />
  
  <AppLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 border-b">
            <div class="flex items-center gap-3">
              <MessageCircle class="w-8 h-8 text-indigo-600" />
              <div>
                <h2 class="text-2xl font-bold text-gray-900">Preguntas de Estudiantes</h2>
                <p class="text-gray-600 mt-1">Responde las preguntas de tus estudiantes</p>
              </div>
            </div>
          </div>

          <div class="p-6">
            <!-- Sin preguntas -->
            <div v-if="questions.data.length === 0" class="text-center py-12">
              <MessageCircle class="w-16 h-16 text-gray-300 mx-auto mb-4" />
              <p class="text-gray-500 text-lg">No hay preguntas pendientes</p>
            </div>

            <!-- Lista de preguntas -->
            <div v-else class="space-y-6">
              <div
                v-for="question in questions.data"
                :key="question.id"
                class="border border-gray-200 rounded-lg p-6 hover:border-indigo-300 transition"
              >
                <!-- Información del curso y lección -->
                <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                  <BookOpen class="w-4 h-4" />
                  <span class="font-semibold">{{ question.lesson.section.course.title }}</span>
                  <span>›</span>
                  <span>{{ question.lesson.section.name }}</span>
                  <span>›</span>
                  <span>{{ question.lesson.name }}</span>
                </div>

                <!-- Pregunta -->
                <div class="flex gap-4 mb-4">
                  <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                      <span class="text-indigo-600 font-semibold text-lg">{{ question.user.name[0] }}</span>
                    </div>
                  </div>
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                      <span class="font-semibold text-gray-900">{{ question.user.name }}</span>
                      <span class="text-xs text-gray-500">{{ formatDate(question.created_at) }}</span>
                    </div>
                    <p class="text-gray-700 text-lg">{{ question.question }}</p>
                  </div>
                </div>

                <!-- Respuestas existentes -->
                <div v-if="question.answers.length > 0" class="ml-16 space-y-3 mb-4">
                  <div
                    v-for="answer in question.answers"
                    :key="answer.id"
                    class="bg-green-50 rounded-lg p-4 border-l-4 border-green-500"
                  >
                    <div class="flex items-center gap-2 mb-2">
                      <span class="font-semibold text-gray-900 text-sm">{{ answer.user.name }}</span>
                      <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded">Instructor</span>
                      <span class="text-xs text-gray-500">{{ formatDate(answer.created_at) }}</span>
                    </div>
                    <p class="text-gray-700">{{ answer.answer }}</p>
                  </div>
                </div>

                <!-- Formulario de respuesta -->
                <div class="ml-16">
                  <div v-if="answeringQuestion === question.id" class="bg-gray-50 rounded-lg p-4">
                    <form @submit.prevent="submitAnswer(question.id)" class="space-y-3">
                      <textarea
                        v-model="answerForms[question.id].answer"
                        rows="3"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Escribe tu respuesta..."
                        required
                      ></textarea>
                      <div class="flex gap-2 justify-end">
                        <button
                          type="button"
                          @click="answeringQuestion = null"
                          class="px-4 py-2 text-gray-700 hover:bg-gray-200 rounded-lg transition"
                        >
                          Cancelar
                        </button>
                        <button
                          type="submit"
                          :disabled="answerForms[question.id]?.processing"
                          class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50"
                        >
                          <Send class="w-4 h-4" />
                          Enviar respuesta
                        </button>
                      </div>
                    </form>
                  </div>
                  <button
                    v-else
                    @click="startAnswering(question.id)"
                    class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                  >
                    {{ question.answers.length > 0 ? 'Añadir otra respuesta' : 'Responder' }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Paginación -->
            <div v-if="questions.last_page > 1" class="mt-6 flex justify-center">
              <nav class="flex gap-2">
                <a
                  v-for="page in questions.last_page"
                  :key="page"
                  :href="`?page=${page}`"
                  :class="[
                    'px-4 py-2 rounded-lg font-medium transition',
                    page === questions.current_page
                      ? 'bg-indigo-600 text-white'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  {{ page }}
                </a>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
