import Selection from '@/Features/Book/Components/Form/Selection';
import { useShowGenreQuery } from '@/Features/Genre/Hooks/useShowGenreQuery';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';

type Props = {
  genreId: number;
  onUnselect?: () => void;
};

export function GenreSelection({ genreId, onUnselect }: Props) {
  const { data, isLoading, isError } = useShowGenreQuery(genreId);

  if (isLoading) {
    return <LoadingSpinner />;
  }

  if (isError || !data) {
    return <DataFetchError />;
  }

  return <Selection onUnselect={onUnselect}>{data.name}</Selection>;
}
