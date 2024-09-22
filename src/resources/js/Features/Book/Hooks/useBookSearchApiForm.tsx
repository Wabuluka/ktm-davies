import { useCallback } from 'react';
import { BookSearchForm, SearchParameters } from '../Components/BookSearchForm';
import { useIndexBookQuery } from './useIndexBookQuery';

type Props = {
  onResultUpdate: () => void;
};

export function useBookSearchApiForm({ onResultUpdate }: Props) {
  const { setQueryParams, ...query } = useIndexBookQuery();
  function onPageChange(page: number) {
    onResultUpdate();
    setQueryParams((prev) => ({ ...prev, page }));
  }
  const handleSubmit = useCallback(
    (params: SearchParameters) => {
      onResultUpdate();
      setQueryParams({ ...params, page: 1 });
    },
    [onResultUpdate, setQueryParams],
  );
  const searchForm = <BookSearchForm onSubmit={handleSubmit} />;

  return {
    ...query,
    searchForm,
    onPageChange,
  };
}
