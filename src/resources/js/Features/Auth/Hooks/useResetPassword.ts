import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';
import { ResetPasswordProps } from '../Types';

export const useResetPassword = ({ token, email }: ResetPasswordProps) => {
  const { data, setData, post, processing, errors, reset } = useForm({
    token,
    email,
    password: '',
    password_confirmation: '',
  });

  useEffect(() => {
    return () => {
      reset('password', 'password_confirmation');
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const onChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    setData(
      event.target.name as
        | 'token'
        | 'email'
        | 'password'
        | 'password_confirmation',
      event.target.value,
    );
  };

  const onSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    post(route('password.store'));
  };

  return {
    data,
    onChange,
    onSubmit,
    processing,
    errors,
  };
};
