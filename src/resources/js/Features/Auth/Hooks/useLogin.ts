import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';

export const useLogin = () => {
  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    password: '',
    remember: false,
  });

  useEffect(() => {
    return () => {
      reset('password');
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const onChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setData(
      e.target.name as 'email' | 'password' | 'remember',
      e.target.type === 'checkbox' ? e.target.checked : e.target.value,
    );
  };

  const onSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    post(route('login'));
  };

  return {
    data,
    onChange,
    onSubmit,
    processing,
    errors,
  };
};
