import { useToast } from '@chakra-ui/react';
import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';
import { useCurrentUser } from './useCurrentUser';

export const useUpdateProfile = () => {
  const user = useCurrentUser();

  const { data, setData, patch, errors, processing, recentlySuccessful } =
    useForm({
      name: user.name,
      email: user.email,
    });

  const onSubmit = (
    e: React.FormEvent<HTMLFormElement> | React.FormEvent<HTMLInputElement>,
  ) => {
    e.preventDefault();
    patch(route('profile.update'));
  };

  const toast = useToast({
    title: 'Profile updated.',
    status: 'success',
  });

  useEffect(() => {
    recentlySuccessful && toast();
  }, [toast, recentlySuccessful]);

  return {
    data,
    setData,
    onSubmit,
    errors,
    processing,
  };
};
