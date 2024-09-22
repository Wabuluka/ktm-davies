import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { Label } from '../Types';
import { formValues } from '../Components/Form';
import { useToast } from '@chakra-ui/react';

export const useCreateLabelMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, formValues, Label['name']>({
    mutationFn: (formValues) => {
      return axios.post(route('label.store'), formValues);
    },
    onSuccess: () => {
      toast({ title: 'Saved successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to save', status: 'error' });
    },
  });
};
