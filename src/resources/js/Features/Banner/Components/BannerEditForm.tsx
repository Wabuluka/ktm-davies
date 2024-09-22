import { useToast } from '@chakra-ui/react';
import { useBannerForm } from '@/Features/Banner/Hooks/useBannerForm';
import { Banner, BannerForm } from '@/Features/Banner';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';

type Props = {
  banner: Banner;
};

export function BannerEditForm({ banner }: Props) {
  const { data, setData, updateBanner, deleteBanner, errors, processing } =
    useBannerForm();
  const toast = useToast();

  function handleSubmit(
    e: React.FormEvent<HTMLFormElement> | React.FormEvent<HTMLInputElement>,
  ) {
    e.preventDefault();
    updateBanner({
      onSuccess: () =>
        toast({ title: `${data.name}をSaved successfully`, status: 'success' }),
      onError: () => {
        toast({ title: 'Failed to save', status: 'error' });
      },
    });
  }

  function handleDeleteButtonClick() {
    if (window.confirm('Are you sure to delete ?')) {
      deleteBanner(banner.id, {
        onSuccess: () =>
          toast({
            title: `${data.name}をDeleted successfully`,
            status: 'success',
          }),
        onError: () => {
          toast({ title: 'Failed to delete', status: 'error' });
        },
      });
    }
  }

  return (
    <>
      <BannerForm
        {...{ data, errors, setData, processing }}
        onSubmit={handleSubmit}
      />
      <DangerButton w="100%" mt={8} onClick={handleDeleteButtonClick}>
        Delete
      </DangerButton>
    </>
  );
}
