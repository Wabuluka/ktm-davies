import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { useToast } from '@chakra-ui/react';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useMutation, useQueryClient } from 'react-query';
import { useCreatorEventListener } from '../Contexts/CreatorEventListnerContext';
import { Creator, CreatorFormData } from '../Types';

type Variables = { id: string | number } & CreatorFormData;

export function useEditCreatorMutation() {
  const toast = useToast();
  const queryClient = useQueryClient();
  const queryKey = useQueryKeys().creators.all;
  const { onUpdateSuccess } = useCreatorEventListener();

  return useMutation<AxiosResponse<Creator>, AxiosError, Variables>({
    mutationFn: ({ id, name, name_kana }) => {
      return axios.patch(route('api.creators.update', id), { name, name_kana });
    },
    onSuccess: ({ data }) => {
      toast({ title: 'Saved successfully', status: 'success' });
      queryClient.invalidateQueries(queryKey);
      onUpdateSuccess?.(data);
    },
    onError: () => {
      toast({ title: 'Failed to save', status: 'error' });
    },
  });
}
