import {
  Banner,
  BannerFormData,
  BannerPlacement,
} from '@/Features/Banner/Types';
import { useForm, usePage } from '@inertiajs/react';

export const useBannerForm = () => {
  const editingBanner = usePage().props.banner as Banner | undefined;
  const {
    data,
    setData,
    post,
    delete: destory,
    errors: rawErrors,
    processing,
  } = useForm<BannerFormData>({
    name: editingBanner?.name ?? '',
    url: editingBanner?.url ?? '',
    new_tab: editingBanner?.new_tab ?? true,
    displayed: editingBanner?.displayed ?? true,
    image: { operation: 'stay' },
  });

  type ExtendedErrors = typeof rawErrors & {
    [key: string]: string;
  };
  const extendedErros: ExtendedErrors = rawErrors;

  const storeBanner = (
    placementId: BannerPlacement['id'],
    options: Parameters<typeof post>[1],
  ) => {
    post(
      route('banner-placements.banners.store', {
        banner_placement: placementId,
      }),
      {
        ...options,
      },
    );
  };

  const updateBanner = (options: Parameters<typeof post>[1]) => {
    if (!editingBanner) {
      return;
    }
    post(
      route('banners.update', {
        banner_placement: editingBanner.placement_id,
        banner: editingBanner,
      }),
      {
        forceFormData: true,
        headers: {
          'X-HTTP-Method-Override': 'PUT',
        },
        ...options,
      },
    );
  };

  const deleteBanner = (
    bannerId: Banner['id'],
    options: Parameters<typeof post>[1],
  ) => {
    destory(route('banners.destroy', { banner: bannerId }), { ...options });
  };

  return {
    data,
    setData,
    storeBanner,
    updateBanner,
    deleteBanner,
    errors: extendedErros,
    processing,
  };
};
