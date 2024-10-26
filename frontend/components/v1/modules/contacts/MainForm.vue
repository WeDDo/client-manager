<script setup>
import {useInsideFormValidation} from "~/composables/useInsideFormValidation.js";
import MainTextInput from "~/components/v1/MainTextInput.vue";
import MainSelectInput from "~/components/v1/MainSelectInput.vue";
import MainDateInput from "~/components/v1/MainDateInput.vue";
import MainEditor from "~/components/v1/MainEditor.vue";
import MainAutocompleteInput from "~/components/v1/MainAutocompleteInput.vue";

const props = defineProps({
    tab: {
        type: Number,
        default: 0,
    }
})

const emit = defineEmits([
    'set-errors',
]);

const form = defineModel('form');

useInsideFormValidation(form.value.errors, emit, props.tab);

const [name] = form.value.defineField('item.name');
const [companyName] = form.value.defineField('item.company_name');
const [position] = form.value.defineField('item.position');
const [phone1] = form.value.defineField('item.phone1');
const [phone2] = form.value.defineField('item.phone2');
const [email1] = form.value.defineField('item.email1');
const [email2] = form.value.defineField('item.email2');
const [birthday] = form.value.defineField('item.birthday');
const [notes] = form.value.defineField('item.notes');
const [address1] = form.value.defineField('item.address1');
const [address2] = form.value.defineField('item.address2');
const [city] = form.value.defineField('item.city');
const [state] = form.value.defineField('item.state');
const [postalCode] = form.value.defineField('item.postal_code');
const [country] = form.value.defineField('item.country');
const [website] = form.value.defineField('item.website');
const [preferredContactMethod] = form.value.defineField('item.preferred_contact_method');
const [status] = form.value.defineField('item.status');
const [lastContactedAt] = form.value.defineField('item.last_contacted_at');
const [partnerId] = form.value.defineField('item.partner_id');

const onSubmit = form.value.handleSubmit((values) => {
    return true;
});

defineExpose({onSubmit});

const items = ref([]);

const search = (event) => {
    items.value = [...Array(10).keys()].map((item) => event.query + '-' + item);
}

</script>

<template>
    <div>
        <form>
            <div class="formgrid grid">
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="name"
                        name="name" label="Name"
                        :errors="form.errors"
                        required
                    />
                    <MainTextInput
                        v-model:value="companyName"
                        name="company_name"
                        label="Company Name"
                        :errors="form.errors"
                    />
                    <MainTextInput
                        v-model:value="position"
                        name="position"
                        label="Position"
                        :errors="form.errors"
                    />
                    <MainTextInput
                        v-model:value="phone1"
                        name="phone1"
                        label="Primary Phone"
                        :errors="form.errors"
                    />
                    <MainTextInput
                        v-model:value="phone2"
                        name="phone2"
                        label="Secondary Phone"
                        :errors="form.errors"
                    />
                    <MainTextInput
                        v-model:value="email1"
                        name="email1"
                        label="Primary Email"
                        :errors="form.errors" />
                    <MainTextInput
                        v-model:value="email2"
                        name="email2"
                        label="Secondary Email"
                        :errors="form.errors"
                    />
                    <MainDateInput
                        v-model:value="birthday"
                        name="birthday"
                        label="Birthday"
                        :errors="form.errors"
                    />
                </div>

                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="address1"
                        name="address1"
                        label="Address 1"
                        :errors="form.errors"
                    />
                    <MainTextInput
                        v-model:value="address2"
                        name="address2"
                        label="Address 2"
                        :errors="form.errors"
                    />
                    <MainTextInput
                        v-model:value="city"
                        name="city"
                        label="City"
                        :errors="form.errors"
                    />
                    <MainTextInput
                        v-model:value="state"
                        name="state"
                        label="State"
                        :errors="form.errors"
                    />
                    <MainTextInput
                        v-model:value="postalCode"
                        name="postal_code"
                        label="Postal Code"
                        :errors="form.errors"
                    />
                    <MainTextInput
                        v-model:value="country"
                        name="country"
                        label="Country"
                        :errors="form.errors"
                    />
                </div>

                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="website"
                        name="website"
                        label="Website"
                        :errors="form.errors"
                    />
                    <MainSelectInput
                        v-model:value="preferredContactMethod"
                        name="preferred_contact_method"
                        label="Preferred Contact Method"
                        :errors="form.errors"
                        :options="['Email', 'Phone', 'Mail']"
                        show-clear
                        simple-options
                    />
                    <MainSelectInput
                        v-model:value="status"
                        name="status"
                        label="Status"
                        :errors="form.errors"
                        :options="['Active', 'Inactive', 'Prospect']"
                        show-clear
                        simple-options
                    />
                    <MainDateInput
                        v-model:value="lastContactedAt"
                        name="last_contacted_at"
                        label="Last Contacted At"
                        show-time
                        :errors="form.errors"
                    />
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainAutocompleteInput
                        v-model:value="partnerId"
                        name="partner_id"
                        label="Partner ID"
                        table="partners"
                        :search-fields="['id_name', 'name']"
                        :errors="form.errors"
                    />
                </div>

                <div class="col-12">
                    <MainEditor
                        v-model:value="notes"
                        name="notes"
                        label="Notes"
                    />
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>
