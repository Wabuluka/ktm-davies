import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useToast } from '@chakra-ui/react';
import { FileData } from 'chonky';
import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';

export const useEditFileMutation = () => {
  const toast = useToast();

  return useMutation<
    AxiosResponse,
    AxiosError,
    { id: FileData['id']; name: FileData['name']; isDir: FileData['isDir'] }
  >({
    mutationFn: ({ id, name, isDir }) => {
      return isDir
        ? axios.put(route('folders.update', [id, { name: name }]))
        : axios.put(route('files.update', [id, { name: name }]));
    },
    onSuccess: () => {
      toast({ title: 'Saved successfully', status: 'success' });
    },
    onError: (error) => {
      const message = isLaravelValidationError(error)
        ? error?.response?.data?.message
        : '';
      toast({
        title: 'Failed to save',
        description: message,
        status: 'error',
      });
    },
  });
};
