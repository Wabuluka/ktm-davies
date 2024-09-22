import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';

export const useConfirmPassword = () => {
  const { data, setData, post, processing, errors, reset } = useForm({
    password: '',
  });

  useEffect(() => {
    return () => {
      reset('password');
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const onChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setData(e.target.name as 'password', e.target.value);
  };

  const onSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    post(route('password.confirm'));
  };

  return {
    data,
    onChange,
    onSubmit,
    processing,
    errors,
  };
};
