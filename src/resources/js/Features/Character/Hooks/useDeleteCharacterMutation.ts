import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { Character } from '@/Features/Character';
import { useToast } from '@chakra-ui/react';

export const useDeleteCharacterMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, Character['id']>({
    mutationFn: (id) => {
      return axios.delete(route('characters.destroy', id));
    },
    onSuccess: () => {
      toast({ title: 'Deleted successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to save', status: 'error' });
    },
  });
};
