import { News, NewsFormData } from '@/Features/News/Types';
import { useForm } from '@inertiajs/react';

type Props =
  | {
      base?: News;
    }
  | undefined;

export const transformNews = (data: NewsFormData) => {
  const { published_at, status, ...rest } = data;
  const postData: NewsFormData = {
    ...rest,
    ...(status === 'willBePublished' ? { status, published_at } : { status }),
  };
  return postData;
};

export const useNewsForm = ({ base }: Props = {}) => {
  const initialValues: NewsFormData = {
    status: base?.status ?? 'draft',
    title: base?.title ?? '',
    slug: base?.slug ?? '',
    content: base?.content ?? '',
    published_at: base?.published_at ?? '',
    category_id: base?.category?.id ? String(base.category.id) : '',
  };
  const form = useForm(initialValues);
  form.transform(transformNews);
  return form;
};

export type UseNewsFormReturn = ReturnType<typeof useNewsForm>;
