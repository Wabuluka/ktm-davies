import Selection from '@/Features/Book/Components/Form/Selection';
import { useShowSeriesQuery } from '@/Features/Series/Hooks/useShowSeriesQuery';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';

type Props = {
  seriesId: number;
  onUnselect: () => void;
};

export function SeriesSelection({ seriesId, onUnselect }: Props) {
  const { data, isLoading, isError } = useShowSeriesQuery(seriesId);

  if (isLoading) {
    return <LoadingSpinner />;
  }

  if (isError || !data) {
    return <DataFetchError />;
  }

  return <Selection onUnselect={onUnselect}>{data.name}</Selection>;
}
