import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { useSortGenreMutation } from '@/Features/Genre/Hooks/useSortGenreMutation';
import { SortButtons } from '@/UI/Components/Form/Button/SortButtons';
import { useQueryClient } from 'react-query';

type Props = {
  genreId: number;
  first?: boolean;
  last?: boolean;
};

export function SortGenreButtons({ genreId, first, last }: Props) {
  const { moveUpMutation, moveDownMutation } = useSortGenreMutation();
  const queryKey = useQueryKeys().genre.all;
  const queryClient = useQueryClient();

  const handleUp = () => {
    moveUpMutation.mutate(genreId, {
      onSuccess: () => queryClient.invalidateQueries(queryKey),
    });
  };

  const handleDown = () => {
    moveDownMutation.mutate(genreId, {
      onSuccess: () => queryClient.invalidateQueries(queryKey),
    });
  };

  return (
    <SortButtons
      onUp={handleUp}
      onDown={handleDown}
      disableUp={first || moveUpMutation.isLoading}
      disableDown={last || moveDownMutation.isLoading}
    />
  );
}
