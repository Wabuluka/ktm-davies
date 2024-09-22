import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useToast } from '@chakra-ui/react';
import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';

export const usePostFileMutation = () => {
  const toast = useToast();

  return useMutation<
    AxiosResponse,
    AxiosError,
    { file: File; folderId: string }
  >({
    mutationFn: ({ file, folderId }) => {
      const formData = new FormData();
      formData.append('file', file);

      return axios.post(route('folders.files.store', folderId), formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
    },
    onSuccess: () => {
      toast({ title: 'アップロードしました', status: 'success' });
    },
    onError: (error) => {
      const message = isLaravelValidationError(error)
        ? error?.response?.data?.message
        : '';
      toast({
        title: 'アップロードに失敗しました',
        description: message,
        status: 'error',
      });
    },
  });
};
