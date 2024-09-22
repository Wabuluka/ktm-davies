import { useForm } from '@inertiajs/react';

export const useUsers = () => {
  const { data, setData, post, errors, processing } = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  });

  const onChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setData(e.target.name as 'name' | 'email' | 'password', e.target.value);
  };

  const onSubmit = (
    e: React.FormEvent<HTMLFormElement> | React.FormEvent<HTMLInputElement>,
  ) => {
    e.preventDefault();
    post(route('users.index'));
  };

  return {
    data,
    setData,
    onChange,
    onSubmit,
    errors,
    processing,
  };
};
