import { NewsCategoryFormData } from '@/Features/NewsCategory/Types';
import { useForm } from '@inertiajs/react';

type Props =
  | {
      category?: NewsCategoryFormData;
    }
  | undefined;

export function useNewsCategoryForm({ category }: Props = {}) {
  const initialValues: NewsCategoryFormData = {
    name: category?.name ?? '',
  };
  return useForm(initialValues);
}

export type UseNewsCategoryFormReturn = ReturnType<typeof useNewsCategoryForm>;
