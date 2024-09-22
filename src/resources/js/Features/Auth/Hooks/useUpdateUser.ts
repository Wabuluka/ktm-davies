import { useToast } from '@chakra-ui/react';
import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';
import { User } from '@/Features/Auth';

type Props = {
  user: User;
};

export const useUpdateUser = ({ user }: Props) => {
  const { data, setData, patch, errors, processing, recentlySuccessful } =
    useForm({
      id: user.id,
      name: user.name,
      email: user.email,
      password: '',
    });

  const onSubmit = (
    e: React.FormEvent<HTMLFormElement> | React.FormEvent<HTMLInputElement>,
  ) => {
    e.preventDefault();
    patch(route('users.update'));
  };

  const toast = useToast({
    title: 'User updated.',
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
