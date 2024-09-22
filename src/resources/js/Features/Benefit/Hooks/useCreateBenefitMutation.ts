import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useToast } from '@chakra-ui/react';
import { BenefitFormData } from '@/Features/Benefit/Types';

export const useCreateBenefitMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, BenefitFormData>({
    mutationFn: ({ name, paid, storeId, thumbnail }) => {
      const formData = new FormData();
      formData.append('name', name);
      formData.append('paid', paid ? '1' : '0');
      storeId && formData.append('store_id', storeId.toString());
      formData.append('thumbnail[operation]', thumbnail.operation);
      thumbnail.file && formData.append('thumbnail[file]', thumbnail.file);

      return axios.post(route('benefits.store'), formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
    },
    onSuccess: () => {
      toast({ title: 'Saved successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to save', status: 'error' });
    },
  });
};
