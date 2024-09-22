import { Banner } from '@/Features/Banner/Types';
import { usePage } from '@inertiajs/react';

export const useEditingBanner = () =>
  usePage().props.banner as Banner | undefined;
