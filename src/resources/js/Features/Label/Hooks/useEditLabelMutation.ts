import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { formValues } from '../Components/Form';
import { useToast } from '@chakra-ui/react';

export const useEditLabelMutation = () => {
  const toast = useToast();

  return useMutation<
    AxiosResponse,
    AxiosError,
    { id: string | number } & formValues
  >({
    mutationFn: ({ id, ...formValues }) => {
      return axios.put(route('label.update', id), formValues);
    },
    onSuccess: () => {
      toast({ title: 'Saved successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to save', status: 'error' });
    },
  });
};
