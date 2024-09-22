import {
  SearchNewsFormParams,
  SearchNewsFormOnChange,
} from '@/Features/News/Components/SearchNewsForm';
import { NewsStatus } from '@/Features/News/Types';
import { Site } from '@/Features/Site';
import { useSessionErrors } from '@/Hooks/Inertia/useSessionErrors';
import { getSearchParams } from '@/Utils/getSearchParams';
import { router } from '@inertiajs/react';
import { useState } from 'react';

type Props = {
  site: Site;
};

export const useIndexNews = ({ site }: Props) => {
  const searchParams = getSearchParams();
  const keyword = searchParams.get('keyword') || '';
  const statuses = searchParams.getAll('statuses[]') as NewsStatus[];
  const [params, setParams] = useState<SearchNewsFormParams>({
    keyword,
    statuses,
  });
  const errors = useSessionErrors();
  const onSearchFormChange: SearchNewsFormOnChange = (param) => {
    switch (param.key) {
      case 'keyword':
        setParams((prev) => ({ ...prev, keyword: param.value }));
        break;
      case 'statuses':
        setParams((prev) => ({ ...prev, statuses: param.value }));
        break;
    }
  };
  function onSearchSubmit() {
    router.get(route('sites.news.index', site), params, {
      preserveState: true,
      preserveScroll: true,
    });
  }
  function onPageChange(page: number) {
    router.get(
      route('sites.news.index', site),
      { ...params, page },
      {
        preserveState: true,
        preserveScroll: true,
      },
    );
  }

  return {
    params,
    errors,
    onSearchFormChange,
    onSearchSubmit,
    onPageChange,
  };
};
