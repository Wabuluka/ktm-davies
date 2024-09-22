import { useMutation } from 'react-query';
import axios from 'axios';
import { Label } from '../Types';
import { useToast } from '@chakra-ui/react';

export const useSortLabelMutation = () => {
  const toast = useToast();

  const useMoveMutation = (direction: 'up' | 'down') =>
    useMutation({
      mutationFn: (id: Label['id']) =>
        axios
          .patch(route(`label.move_${direction}`, id))
          .then((result) => result.data),
      onSuccess: () => {
        toast({
          title: 'Saved the sorting order successfully',
          status: 'success',
        });
      },
      onError: () => {
        toast({ title: 'Failed to save the sorting order', status: 'error' });
      },
    });

  return {
    moveUpMutation: useMoveMutation('up'),
    moveDownMutation: useMoveMutation('down'),
  };
};
