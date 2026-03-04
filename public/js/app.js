const { createApp, ref, onMounted } = Vue;

const app = createApp({
    setup() {
        const currentTime = ref('');

        const updateTime = () => {
            const now = new Date();
            currentTime.value = now.toLocaleTimeString('id-ID');
        };

        onMounted(() => {
            updateTime();
            setInterval(updateTime, 1000);
        });

        return { currentTime };
    }
});

/* Axios Base Config */
axios.defaults.baseURL = "/api";

/* Toast */
app.use(VueToastification.default, {
    timeout: 3000,
    position: "top-right"
});

/* Router */
app.use(router);

app.mount('#app');
