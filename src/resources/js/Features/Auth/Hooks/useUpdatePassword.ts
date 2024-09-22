import { useToast } from '@chakra-ui/react';
import { useForm } from '@inertiajs/react';
import { useEffect, useRef } from 'react';

export const useUpdatePassword = () => {
  const passwordInput = useRef<HTMLInputElement>(null);
  const currentPasswordInput = useRef<HTMLInputElement>(null);

  const { data, setData, errors, put, reset, processing, recentlySuccessful } =
    useForm({
      current_password: '',
      password: '',
      password_confirmation: '',
    });

  const onSubmit = (
    e: React.FormEvent<HTMLFormElement> | React.FormEvent<HTMLInputElement>,
  ) => {
    e.preventDefault();

    put(route('password.update'), {
      preserveScroll: true,
      onSuccess: () => reset(),
      onError: () => {
        if (errors.password) {
          reset('password', 'password_confirmation');
          passwordInput.current && passwordInput.current.focus();
        }

        if (errors.current_password) {
          reset('current_password');
          currentPasswordInput.current && currentPasswordInput.current.focus();
        }
      },
    });
  };

  const toast = useToast({
    title: 'Password updated.',
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
    passwordInput,
    currentPasswordInput,
  };
};
