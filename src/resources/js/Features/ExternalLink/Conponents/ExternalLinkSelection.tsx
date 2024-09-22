import Selection from '@/Features/Book/Components/Form/Selection';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { useShowExternalLinkQuery } from '../Hooks/useShowExternalLinkQuery';

type Props = {
  externalLinkId: number;
  onUnselect: () => void;
};

export function ExternalLinkSelection({ externalLinkId, onUnselect }: Props) {
  const { data, isLoading, isError } = useShowExternalLinkQuery(externalLinkId);

  if (isLoading) {
    return <LoadingSpinner />;
  }

  if (isError || !data) {
    return <DataFetchError />;
  }

  return <Selection onUnselect={onUnselect}>{data.title}</Selection>;
}
