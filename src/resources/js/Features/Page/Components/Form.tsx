import { Site } from '@/Features/Site/Types';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { ButtonGroup, VStack } from '@chakra-ui/react';
import { FC } from 'react';
import { useEditPage } from '../Hooks/useEditPage';
import { Page } from '../Types';
import { HTMLField } from './Form/HTMLField';
import { PageTitleField } from './Form/PageTitleField';
import { SlugField } from './Form/SlugField';
import { PagePreviewButton } from '@/Features/Page/Components/Form/PagePreviewButton';

type Props = {
  site: Site;
  page: Partial<Page>;
};

export const Form: FC<Props> = ({ site, page }) => {
  const { data, setData, editPage, errors, processing } = useEditPage({
    site,
    page,
  });

  return (
    <form onSubmit={editPage}>
      <VStack align="stretch" spacing={8}>
        <PageTitleField {...{ data, errors, setData }} />

        <SlugField {...{ data, errors, setData }} isReadOnly />

        <HTMLField {...{ data, errors, setData }} />

        <ButtonGroup spacing={2}>
          <PagePreviewButton
            formData={data}
            pageId={page.id}
            siteId={site.id}
            isDisabled={processing}
          />
          <PrimaryButton type="submit" isLoading={processing}>
            Save
          </PrimaryButton>
        </ButtonGroup>
      </VStack>
    </form>
  );
};
