import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { Benefit } from '@/Features/Benefit';
import { useToast } from '@chakra-ui/react';

export const useDeleteBenefitMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, Benefit['id']>({
    mutationFn: (id) => {
      return axios.delete(route('benefits.destroy', id));
    },
    onSuccess: () => {
      toast({ title: 'Deleted successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to delete', status: 'error' });
    },
  });
};
