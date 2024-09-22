import Selection from '@/Features/Book/Components/Form/Selection';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { useShowCreatorQuery } from '../Hooks/useShowCreatorQuery';

type Props = {
  creatorId: string | number;
  onUnselect: () => void;
};

export function CreatorSelection({ creatorId, onUnselect }: Props) {
  const { data: creator, isLoading, isError } = useShowCreatorQuery(creatorId);

  if (isLoading) {
    return <LoadingSpinner />;
  }

  if (isError || !creator) {
    return <DataFetchError />;
  }

  return (
    <Selection onUnselect={onUnselect}>
      {creator.name} {!!creator.name_kana && <>({creator.name_kana})</>}
    </Selection>
  );
}
