import { useForm } from '@inertiajs/react';

export const useDeleteUser = (userId: number) => {
  const { delete: destroy, processing, errors } = useForm();

  const deleteUser = (options: Parameters<typeof destroy>['1']) => {
    destroy(route('users.destroy', { id: userId }), options);
  };

  return {
    deleteUser,
    errors,
    processing,
  };
};
