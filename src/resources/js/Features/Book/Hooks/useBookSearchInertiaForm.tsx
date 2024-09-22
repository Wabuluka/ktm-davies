import { router } from '@inertiajs/react';
import { useCallback } from 'react';
import { BookSearchForm, SearchParameters } from '../Components/BookSearchForm';
import { BookStatus } from '../Types';
import { getSearchParams } from '@/Utils/getSearchParams';

const bookStatuses: BookStatus[] = ['draft', 'willBePublished', 'published'];

type Props = {
  onSubmit: (params: SearchParameters) => void;
};

export function useBookSearchInertiaForm({ onSubmit }: Props) {
  const searchParams = getSearchParams();
  const keyword = searchParams.get('keyword') || '';
  const sites = searchParams.getAll('sites[]');
  const statuses = bookStatuses.filter((status) =>
    searchParams.getAll('statuses[]').includes(status),
  );
  const onPageChange = useCallback(
    (page: number) => {
      router.get(
        route('books.index'),
        { keyword, sites, statuses, page },
        {
          preserveState: true,
          preserveScroll: true,
        },
      );
    },
    [keyword, sites, statuses],
  );
  const handleSubmit = useCallback(
    (params: SearchParameters) => {
      onSubmit(params);
      router.get(
        route('books.index'),
        { ...params, page: 1 },
        {
          preserveState: true,
          preserveScroll: true,
        },
      );
    },
    [onSubmit],
  );
  const initialValues: SearchParameters = {
    keyword,
    sites,
    statuses,
  };
  const searchForm = (
    <BookSearchForm onSubmit={handleSubmit} initialValues={initialValues} />
  );

  return {
    searchForm,
    onPageChange,
  };
}
