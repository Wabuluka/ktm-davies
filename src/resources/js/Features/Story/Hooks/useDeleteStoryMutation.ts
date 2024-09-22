import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { Story } from '@/Features/Story';
import { useToast } from '@chakra-ui/react';

export const useDeleteStoryMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, Story['id']>({
    mutationFn: (id) => {
      return axios.delete(route('stories.destroy', id));
    },
    onSuccess: () => {
      toast({ title: 'Deleted successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to delete', status: 'error' });
    },
  });
};
