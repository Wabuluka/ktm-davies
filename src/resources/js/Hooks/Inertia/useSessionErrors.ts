import { usePage } from '@inertiajs/react';

export function useSessionErrors() {
  return usePage().props.errors;
}
