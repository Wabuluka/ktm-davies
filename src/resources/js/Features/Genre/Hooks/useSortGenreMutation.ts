import { useMutation } from 'react-query';
import axios from 'axios';
import { Genre } from '../Types';
import { useToast } from '@chakra-ui/react';

export const useSortGenreMutation = () => {
  const toast = useToast();

  const useMoveMutation = (direction: 'up' | 'down') =>
    // console.log(direction)
    useMutation({
      mutationFn: (id: Genre['id']) =>
        axios
          .patch(route(`genre.move_${direction}`, id))
          .then((result) => {
            console.log(result)
            result.data
          }),
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
