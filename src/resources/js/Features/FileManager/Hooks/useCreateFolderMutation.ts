import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useToast } from '@chakra-ui/react';
import { FileData } from 'chonky';
import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';

export const useCreateFolderMutation = () => {
  const toast = useToast();

  return useMutation<
    AxiosResponse[],
    AxiosError,
    { name: FileData['name']; parentId: FileData['id'] }
  >({
    mutationFn: ({ name, parentId }) => {
      return axios.post(route('folders.store', { name, parent_id: parentId }));
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
