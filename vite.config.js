import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    // On a shared dev machine the browser runs elsewhere: listen on every interface on VITE_PORT and point hot reload
    // at the APP_URL host. Without VITE_PORT, Vite keeps its defaults.
    const devMachineServer = env.VITE_PORT
        ? {
              host: '0.0.0.0',
              port: Number(env.VITE_PORT),
              strictPort: true,
              hmr: { host: new URL(env.APP_URL).hostname },
          }
        : {};

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
                fonts: [
                    bunny('Instrument Sans', {
                        weights: [400, 500, 600],
                    }),
                ],
            }),
            tailwindcss(),
        ],
        server: {
            ...devMachineServer,
            cors: true,
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});
