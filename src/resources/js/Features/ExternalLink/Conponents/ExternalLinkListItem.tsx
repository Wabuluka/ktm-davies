import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { PreviewableThumbnail } from '@/UI/Components/MediaAndIcons/PreviewableThumbnail';
import { ExternalLinkIcon } from '@chakra-ui/icons';
import { Center, Link, Radio, Td, Tr } from '@chakra-ui/react';
import { ExternalLink } from '../Types';

type Props = {
  externalLink: ExternalLink;
  onEdit: (externalLink: ExternalLink) => void;
  selectable?: boolean | ((externalLink: ExternalLink) => boolean);
};

export function ExternalLinkListItem({
  externalLink,
  onEdit,
  selectable = true,
}: Props) {
  const isSelectable =
    typeof selectable === 'function' ? selectable(externalLink) : selectable;
  function handleEditButtonClick() {
    onEdit(externalLink);
  }

  return (
    <Tr>
      {selectable !== false && (
        <Td>
          <Radio value={`${externalLink.id}`} isDisabled={!isSelectable} />
        </Td>
      )}
      <Td>{externalLink.title}</Td>
      <Td>
        <Link href={externalLink.url} isExternal>
          {externalLink.url}
          <ExternalLinkIcon ml={2} />
        </Link>
      </Td>
      <Td p={0}>
        {!!externalLink.thumbnail && (
          <Center p={1}>
            <PreviewableThumbnail
              previewTriggerProps={{
                'aria-label': 'Preview thumbnail',
              }}
              imageProps={{
                src: externalLink.thumbnail.original_url,
                alt: '',
              }}
            />
          </Center>
        )}
      </Td>
      <Td>
        <EditButton
          onClick={handleEditButtonClick}
          aria-label={`Edit ${externalLink.title}`}
        />
      </Td>
    </Tr>
  );
}
