import Selection from '@/Features/Book/Components/Form/Selection';
import { useShowLabelQuery } from '@/Features/Label/Hooks/useShowLabelQuery';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';

type Props = {
  labelId: number;
  onUnselect: () => void;
};

export function LabelSelection({ labelId, onUnselect }: Props) {
  const { data, isLoading, isError } = useShowLabelQuery(labelId);

  if (isLoading) {
    return <LoadingSpinner />;
  }

  if (isError || !data) {
    return <DataFetchError />;
  }

  return <Selection onUnselect={onUnselect}>{data.name}</Selection>;
}
