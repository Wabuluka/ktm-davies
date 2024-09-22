import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { Label } from '../Types';
import { useToast } from '@chakra-ui/react';

export const useDeleteLabelMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, Label['id']>({
    mutationFn: (id) => {
      return axios.delete(route('label.destroy', id));
    },
    onSuccess: () => {
      toast({ title: 'Deleted successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to delete', status: 'error' });
    },
  });
};
