<script setup>
import moment from "moment";

const props = defineProps({
    budget: {
        type: Object,
        default: null,
    },
});

const mainStore = useMainStore();
const daysOfWeek = ref(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']);
// const selectedMonth = ref(moment());
const latestClickedCalendarDay = ref();

// const calendarDays = ref();

const overlayPanelRef = ref();

const calendarDays = computed(() => {
    const daysInMonth = Array.from({ length: date.value.daysInMonth() }).map((_, index) => date.value.clone().startOf('month').add(index, 'day'));

    const startDay = daysInMonth[0].day();
    const endDay = 6 - daysInMonth[daysInMonth.length - 1].day();

    const prevMonthDays = Array.from({ length: startDay }).map((_, index) => date.value.clone().startOf('month').subtract(startDay - index, 'day'));
    const nextMonthDays = Array.from({ length: endDay }).map((_, index) => date.value.clone().endOf('month').add(index + 1, 'day'));

    return [...prevMonthDays, ...daysInMonth, ...nextMonthDays];
});
//
// function getCalendarDays() {
//     const daysInMonth = Array.from({ length: date.value.daysInMonth() }).map((_, index) => date.value.clone().startOf('month').add(index, 'day'));
//
//     const startDay = daysInMonth[0].day();
//     const endDay = 6 - daysInMonth[daysInMonth.length - 1].day();
//
//     const prevMonthDays = Array.from({ length: startDay }).map((_, index) => date.value.clone().startOf('month').subtract(startDay - index, 'day'));
//     const nextMonthDays = Array.from({ length: endDay }).map((_, index) => date.value.clone().endOf('month').add(index + 1, 'day'));
//
//     return [...prevMonthDays, ...daysInMonth, ...nextMonthDays];
// }

function getCalendarDayBackgroundColor(calendarDay) {
    if (latestClickedCalendarDay?.value?.isSame(calendarDay, 'day')) {
        return 'var(--red-300)'
    }

    if (calendarDay.isSame(moment(), 'day')) {
        return 'var(--gray-200)';
    }

    if (calendarDay.isSame(date.value, 'month')) {
        return '';
    } else {
        return 'var(--gray-400)';
    }
}

function handleClickPreviousMonth() {
    date.value = date.value.clone().subtract(1, 'month');
}

function handleClickNextMonth() {
    date.value = date.value.clone().add(1, 'month');
}

const emit = defineEmits([
    'calendar-day-click'
]);

function handleClickCalendarDay(event, calendarDay) {
    // setCalendarDays();
    emit('calendar-day-click', { event, calendarDay});
    if (calendarDay.month() === date.value.month()) {
        overlayPanelRef.value.toggle(event);
        latestClickedCalendarDay.value = calendarDay;
    } else if (calendarDay.isBefore(date.value, 'month')) {
        handleClickPreviousMonth();
    } else if (calendarDay.isAfter(date.value, 'month')) {
        handleClickNextMonth();
    }
}

function getCalendarEventsLength(calendarDay) {
    return (props.budget?.additional?.calendar_events?.items?.filter(event => calendarDay.isSame(event?.date, 'day')).length)
}

// function setCalendarDays() {
//     calendarDays.value = getCalendarDays();
// }

const date = defineModel('date');

defineExpose({latestClickedCalendarDay, daysOfWeek});

// onMounted(() => {
//     setCalendarDays();
// });
</script>

<template>
    <div class="my-2">
        <div class="flex justify-content-center align-items-center">
            <i
                class="pi pi-angle-left cursor-pointer"
                @click="handleClickPreviousMonth"
            />
            <div class="mx-2">
                {{ date.format('YYYY-MM') }}
            </div>
            <i
                class="pi pi-angle-right cursor-pointer"
                @click="handleClickNextMonth"
            />
        </div>
        <div class="flex justify-content-between">
            <div
                v-for="(dayOfWeek, index) in daysOfWeek"
                :key="index"
                style="width: 14%"
                class="text-center"
            >
                {{ dayOfWeek }}
            </div>
        </div>
        <div class="flex justify-content-between flex-wrap">
            <Card
                v-for="(calendarDay, key) in calendarDays"
                :key="key"
                :style="{width: '14%', background: getCalendarDayBackgroundColor(calendarDay)}"
                class="cursor-pointer"
                @click="handleClickCalendarDay($event, calendarDay)"
            >
                <template #content>
                    <div
                        class="text-center"
                        style="color: var(--gray-500)"
                    >
                        {{ calendarDay.format('D') }}
                    </div>

                    <div class="text-center">
                        <div>
                            <Badge
                                size="small"
                                class="mb-1"
                                :style="{visibility: 'hidden'}"
                            >
                                {{ getCalendarEventsLength(calendarDay) }}
                            </Badge>
                        </div>
                    </div>

                    <div
                        v-if="mainStore.settings?.calendar_show_day_expenses && calendarDay.month() === date.month()"
                        class="text-center text-xs"
                    >
                        <div>
                            {{ budget?.additional?.day_expenses[calendarDay.format('YYYY-MM-DD')] ?? '0.00' }}{{ mainStore?.settings?.currency }}
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <OverlayPanel ref="overlayPanelRef">
            <div>
                <div>Spent this day: {{ budget?.additional?.day_expenses[latestClickedCalendarDay.format('YYYY-MM-DD')] ?? 0 }}{{ mainStore.settings.currency }}</div>
            </div>
        </OverlayPanel>
    </div>
</template>

<style>
    .p-card {
        border-radius: 5px;
        margin-top: 0.1rem;
        margin-bottom: 0.1rem;
    }

    .p-card .p-card-body {
         padding: 0;
    }
</style>
