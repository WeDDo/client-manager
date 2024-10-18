<script setup>
import {useFormHelper} from "~/composables/useFormHelper.js";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import MainForm from "~/components/v1/modules/login/MainForm.vue";
import {useAuthHelper} from "~/composables/useAuthHelper.js";
import {useMainStore} from "~/stores/main.js";
import Container from "~/components/v1/Container.vue";
import {loginSchema} from "~/schemas/loginSchema.js";

const { public: { baseURL } } = useRuntimeConfig();

const mainStore = useMainStore();

const router = useRouter();
const toast = useToast();
const token = useCookie('token');

const form = useForm({
    validationSchema: loginSchema,
    initialValues: {
        item: {
            email: '',
            password: '',
        }
    }
});

const mainFormRef = ref();
let tabs = reactive([
  { name: "Main", ref: mainFormRef, errors: {} },
]);

const authHelper = useAuthHelper();
const formHelper = useFormHelper(tabs);
const fetchHelper = useFetchHelper();

async function handleLogin() {
  if(!await formHelper.validateForm(formHelper.errors)) {
    return ;
  }

  mainStore.actionLoading = true;

  let csrfToken = null;
  await $fetch(`${baseURL}/sanctum/csrf-cookie`, {
    method: 'GET',
    onResponse({ response }) {
      if (response.ok) {
        csrfToken = response._data.csrf_token;
      } else {
        fetchHelper.handleResponseError(response);
      }
    },
  })

  await $fetch(`${baseURL}/login`, {
    method: 'POST',
    body: form.values.item,
    headers: {
      'X-CSRF-TOKEN': csrfToken,
    },
    onResponse({ response }) {
      if (response.ok) {
        authHelper.setUserInLocalStorage(response._data);
        token.value = response._data.token;
        router.push('/');
      } else {
        fetchHelper.handleResponseError(response);
      }
      mainStore.actionLoading = false;
    },
  })
}

function handleKeyDown(event) {
  if (event.key === 'Enter') {
    handleLogin();
  }
}
</script>

<template>
    <Container
        class="p-2"
        @keydown="handleKeyDown"
    >
      <template #content>
        <div class="px-1 py-6 sm:px-5 sm:py-6 mt-8 w-12 sm:w-6 m-auto">
          <main-form
              ref="mainFormRef"
              v-model:form="form"
              :tab="0"
              @handle-submit="handleLogin()"
              @set-errors="formHelper.setErrors"
          />

          <Button
              label="Login"
              class="button w-full mt-2"
              :loading="mainStore.actionLoading"
              @click="handleLogin()"
          />
        </div>
      </template>
    </Container>
</template>

<style scoped>

</style>
