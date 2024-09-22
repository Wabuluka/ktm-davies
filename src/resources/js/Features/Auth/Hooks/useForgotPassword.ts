import { useForm } from '@inertiajs/react';

export const useForgotPassword = () => {
  const { data, setData, post, processing, errors } = useForm({
    email: '',
  });

  const onChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setData(e.target.name as 'email', e.target.value);
  };

  const onSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    post(route('password.email'));
  };

  return {
    data,
    onChange,
    onSubmit,
    processing,
    errors,
  };
};
