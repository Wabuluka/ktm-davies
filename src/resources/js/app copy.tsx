import './bootstrap';

import { ChakraProvider } from '@chakra-ui/react';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { QueryClientProvider } from 'react-query';
import { ReactQueryDevtools } from 'react-query/devtools';
import { queryClient } from './Lib/react-query';
import { theme } from './theme';
import { toastOptions } from './toastOptions';

const appName =
  window.document.getElementsByTagName('title')[0]?.innerText || 'KTCMS';

createInertiaApp({
  title: (title) => `${title} | ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.tsx`,
      import.meta.glob(['./Pages/**/*.tsx', '../images/**']),
    ),
  progress: {
    color: '#4B5563',
  },
  setup({ el, App, props }) {
    const root = createRoot(el);
    root.render(
      <ChakraProvider theme={theme} {...toastOptions}>
        <QueryClientProvider client={queryClient}>
          <ReactQueryDevtools initialIsOpen={false} position={'bottom-right'} />
          <App {...props} />
        </QueryClientProvider>
      </ChakraProvider>,
    );
  },
});
