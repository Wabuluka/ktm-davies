import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { LaravelValidationError } from '@/Features/Misc/Api/Types';
import { NewsFormData, NewsPreview } from '@/Features/News/Types';
import { transformNews } from '@/Features/News/Hooks/useNewsForm';

type NewsPreviewResponse = {
  preview: NewsPreview;
};

type Props = {
  siteId: string | number;
  onSuccess: (response: NewsPreviewResponse) => void;
  onError?: (error: AxiosError<LaravelValidationError>) => void;
};

export function useNewsPreviewMutation({ siteId, onSuccess, onError }: Props) {
  return useMutation<
    AxiosResponse<NewsPreviewResponse>,
    AxiosError<LaravelValidationError>,
    {
      id?: string | number;
      formData: NewsFormData;
    }
  >({
    mutationFn: ({ id, formData }) => {
      const payload = {
        ...transformNews(formData),
        // NOTE: If the value is null, the eyecatch will not be included in the request parameters,
        // so it will be converted to an empty string.
        eyecatch: formData.eyecatch === null ? '' : formData.eyecatch,
      };

      return axios.post(
        route('api.sites.news.preview', { site: siteId, news: id }),
        payload,
        {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        },
      );
    },
    onSuccess: (response) => {
      onSuccess?.(response.data);
    },
    onError: (error) => {
      onError?.(error);
    },
  });
}
