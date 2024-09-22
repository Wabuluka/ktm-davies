import { Paginator } from '@/Api/Types';
import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { ExternalLink } from '@/Features/ExternalLink';
import axios from 'axios';
import { useState } from 'react';
import { useQuery } from 'react-query';

export type ExternalLinkIndexQueryParams = {
  keyword?: string;
  page?: number;
};

const fetchExternalLinks = (params?: ExternalLinkIndexQueryParams) => {
  return axios
    .get(route('external-links.index'), {
      params,
    })
    .then((response) => response.data);
};

export function useIndexExternalLinkQuery(
  params?: ExternalLinkIndexQueryParams,
  options: Parameters<typeof useQuery<Paginator<ExternalLink>, Error>>[2] = {
    staleTime: 0,
  },
) {
  const [queryParams, setQueryParams] = useState<ExternalLinkIndexQueryParams>(
    params || {
      page: 1,
      keyword: '',
    },
  );
  const queryKeys = useQueryKeys();
  const queryKey = queryKeys.externalLinks.index(queryParams);
  const query = useQuery<Paginator<ExternalLink>, Error>(
    queryKey,
    () => fetchExternalLinks(queryParams),
    options,
  );

  return { ...query, setQueryParams, queryKey };
}
