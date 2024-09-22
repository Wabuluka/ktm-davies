import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { useSortLabelMutation } from '@/Features/Label/Hooks/useSortLabelMutation';
import { SortButtons } from '@/UI/Components/Form/Button/SortButtons';
import { useQueryClient } from 'react-query';

type Props = {
  labelId: number;
  first?: boolean;
  last?: boolean;
};

export function SortLabelButtons({ labelId, first, last }: Props) {
  const { moveUpMutation, moveDownMutation } = useSortLabelMutation();
  const queryKey = useQueryKeys().label.all;
  const queryClient = useQueryClient();

  const handleUp = () => {
    moveUpMutation.mutate(labelId, {
      onSuccess: () => queryClient.invalidateQueries(queryKey),
    });
  };

  const handleDown = () => {
    moveDownMutation.mutate(labelId, {
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
