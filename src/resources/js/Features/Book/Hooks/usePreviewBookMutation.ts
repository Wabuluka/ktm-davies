import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { BookFormData, BookPreview } from '@/Features/Book/Types';
import { transformBookData } from '@/Features/Book/Hooks/useBookForm';
import { LaravelValidationError } from '@/Features/Misc/Api/Types';

type PreviewBookResponse = {
  previews: BookPreview[];
};

type Props = {
  onSuccess: (response: PreviewBookResponse) => void;
  onError?: (error: AxiosError<LaravelValidationError>) => void;
};

export function ensureBooleanValueWhenValidated(value: unknown): 0 | 1 {
  if (value === 'false' || value === '0') {
    return 0;
  }
  if (!value) {
    return 0;
  }

  return 1;
}

export function usePreviewBookMutation({ onSuccess, onError }: Props) {
  return useMutation<
    AxiosResponse<PreviewBookResponse>,
    AxiosError<LaravelValidationError>,
    {
      id?: string | number;
      form: BookFormData;
    }
  >({
    mutationFn: ({ id, form }) => {
      const {
        cover,
        ebook_only,
        special_edition,
        limited_edition,
        adult,
        bookstores,
        ebookstores,
        blocks,
        ...transformed
      } = transformBookData(form);

      const payload = {
        cover: cover === null ? '' : cover,
        ebook_only: ensureBooleanValueWhenValidated(ebook_only),
        special_edition: ensureBooleanValueWhenValidated(special_edition),
        limited_edition: ensureBooleanValueWhenValidated(limited_edition),
        adult: ensureBooleanValueWhenValidated(adult),
        bookstores: bookstores?.map(({ is_primary, ...bookstore }) => ({
          ...bookstore,
          is_primary: ensureBooleanValueWhenValidated(is_primary),
        })),
        ebookstores: ebookstores?.map(({ is_primary, ...ebookstore }) => ({
          ...ebookstore,
          is_primary: ensureBooleanValueWhenValidated(is_primary),
        })),
        blocks: {
          upsert: blocks.upsert.map(({ displayed, ...block }) => ({
            ...block,
            displayed: ensureBooleanValueWhenValidated(displayed),
          })),
          deleteIds: blocks.deleteIds,
        },
        ...transformed,
      };

      return axios.post(route('api.books.preview', id), payload, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
    },
    onSuccess: (response) => {
      onSuccess?.(response.data);
    },
    onError: (error) => {
      onError?.(error);
    },
  });
}
