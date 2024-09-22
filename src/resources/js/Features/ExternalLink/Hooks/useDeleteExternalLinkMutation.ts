import { useToast } from '@chakra-ui/react';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useMutation } from 'react-query';

export const useDeleteExternalLinkMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, string | number>({
    mutationFn: (id) => {
      return axios.delete(route('external-links.destroy', id));
    },
    onSuccess: () => {
      toast({ title: 'Deleted successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to delete', status: 'error' });
    },
  });
};
