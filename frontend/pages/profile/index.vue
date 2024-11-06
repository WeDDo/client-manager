<script setup>
import {useMainStore} from "~/stores/main.js";
import {useFormHelper} from "~/composables/useFormHelper.js";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import MainMenuBar from "~/components/v1/MainMenuBar.vue";

const {public: {baseURL}} = useRuntimeConfig();

const token = useCookie('token');

const toast = useToast();

const userProfileImage = ref();

const mainStore = useMainStore();

// const formValues = reactive({
//     item: {
//         user_profile_image_attachment_id: null,
//         user_id: null,
//     }
// })

const formHelper = useFormHelper();
const fetchHelper = useFetchHelper();

const {data, status, error, refresh} = await useFetch(`${baseURL}/profile`, {
    headers: {
        authorization: `Bearer ${token.value}`
    },
});

if (!error.value) {
    formHelper.setFormValues(data.value);
    userProfileImage.value = data.value.additional.user_profile_image;
} else {
    fetchHelper.handleUseFetchError(error);
}

async function updateProfileImage(event) {
    const formData = new FormData();
    formData.append('file', event.files[0]);

    await $fetch(`${baseURL}/profile/update-user-image`, {
        method: 'POST',
        body: formData,
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            userProfileImage.value = response._data.user_profile_image;
        },
    })
}
</script>

<template>
    <div>
        <MainMenuBar/>
        <div class="m-2 p-2">
            <div class="grid mt-4">
                <div class="col-12 sm:col-4 md:col-3 lg:col-2">
                    <div class="text-center p-3 border-round-sm font-bold">
                        <div class="m-auto">
                            <Avatar
                                :image="userProfileImage ?? 'https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png'"
                                class="h-full w-full"
                                style="min-width: 125px; min-height: 125px"
                            />

                            <div class="mt-2">
                                <FileUpload
                                    mode="basic"
                                    name="files[]"
                                    accept="image/*"
                                    class="w-full h-2rem"
                                    :auto="true"
                                    choose-label="Change"
                                    :max-file-size="10000000"
                                    custom-upload
                                    @upload="toast.add({ severity: 'success', summary: 'Imported successfully', life: 2000 })"
                                    @uploader="updateProfileImage($event)"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 sm:col-8 md:col-9 lg:col-10 border-1 border-round border-200">
                    <div class="p-3 border-round-sm">
                        <div class="m-auto">
                            <div class="text-2xl text-center sm:text-left">
                                User profile
                            </div>
                            <div class="mt-2">
                                <!--                            Name: {{ formValues.item.name }}-->
                            </div>
                            <div class="mt-2">
                                <!--                            Email: {{ formValues.item.email }}-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.not-interactive {
    pointer-events: none;
}
</style>
