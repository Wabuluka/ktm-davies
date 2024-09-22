import { router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import { RequestPayload, GlobalEventCallback } from '@inertiajs/core/types';
import { useCallback, useState } from 'react';
import { BookFormData } from '../Types';

type Props = {
  initialValues?: BookFormData;
};

export function transformBookData(data: BookFormData) {
  const {
    cover,
    published_at,
    sites,
    characters,
    creations,
    bookstores,
    ebookstores,
    benefits,
    stories,
    ...rest
  } = data;

  return {
    ...rest,
    ...(typeof cover === 'undefined' ? {} : { cover }),
    ...(sites.length === 0 ? { sites: null } : { sites }),
    ...(characters.length === 0 ? { characters: null } : { characters }),
    ...(creations.length === 0 ? { creations: null } : { creations }),
    ...(ebookstores.length === 0 ? { ebookstores: null } : { ebookstores }),
    ...(benefits.length === 0 ? { benefits: null } : { benefits }),
    ...(stories.length === 0 ? { stories: null } : { stories }),
    ...(bookstores?.length === 0 ? { bookstores: null } : { bookstores }),
    ...(data.status === 'willBePublished' ? { published_at } : {}),
  };
}

export const useBookForm = ({ initialValues }: Props) => {
  const [processing, setProcessing] = useState(false);
  const { data, setData, post, errors, setError, clearErrors, isDirty } =
    useForm<BookFormData>(initialValues);

  const setErrors: GlobalEventCallback<'error'> = useCallback(
    (errors) => {
      Object.entries(errors).forEach(([key, value]) =>
        setError(key as keyof BookFormData, value),
      );
    },
    [setError],
  );

  const storeBook = useCallback(
    (options: Parameters<typeof post>[1]) => {
      clearErrors();
      router.post(
        route('books.store'),
        transformBookData(data) as unknown as RequestPayload,
        {
          ...options,
          onBefore: () => setProcessing(true),
          onError: setErrors,
          onFinish: () => setProcessing(false),
          forceFormData: true,
        },
      );
    },
    [clearErrors, data, setErrors],
  );

  const updateBook = useCallback(
    (id: number, options: Parameters<typeof post>[1]) => {
      clearErrors();
      router.post(
        route('books.update', id),
        transformBookData(data) as unknown as RequestPayload,
        {
          ...options,
          onBefore: () => setProcessing(true),
          onError: setErrors,
          onFinish: () => setProcessing(false),
          forceFormData: true,
          headers: {
            'X-HTTP-Method-Override': 'PATCH',
          },
        },
      );
    },
    [clearErrors, data, setErrors],
  );

  return {
    data,
    setData,
    storeBook,
    updateBook,
    errors,
    processing,
    isDirty,
  };
};
