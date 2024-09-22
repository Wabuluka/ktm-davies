import { useForm } from '@inertiajs/react';

export const useDeleteCurrentUser = () => {
  const {
    data,
    setData,
    delete: destroy,
    processing,
    reset,
    errors,
    clearErrors,
  } = useForm({
    password: '',
  });

  const deleteUser = (options: Parameters<typeof destroy>['1']) => {
    destroy(route('profile.destroy'), options);
  };

  return {
    data,
    setData,
    deleteUser,
    reset,
    errors,
    clearErrors,
    processing,
  };
};
