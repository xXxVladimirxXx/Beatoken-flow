function parseMetaAttributes(nft) {
    if (nft.metadata && nft.metadata.name) {
        if (nft.metadata.attributes) {
            var type = nft.metadata.attributes.find(e => e.attribute_type == 'type')
            var author = nft.metadata.attributes.find(e => e.attribute_type == 'author')
            var creator = nft.metadata.attributes.find(e => e.attribute_type == 'creator')
            var creator_avatar = nft.metadata.attributes.find(e => e.attribute_type == 'creator_avatar')
            var congratulations_video = nft.metadata.attributes.find(e => e.attribute_type == 'congratulations_video')
            var numbering = nft.metadata.attributes.find(e => e.attribute_type == 'numbering')
            if (type) nft.type = type
            if (author) nft.author = author
            if (creator) nft.creator = creator
            if (creator_avatar) nft.creator_avatar = creator_avatar
            if (congratulations_video) nft.congratulations_video = congratulations_video
            if (numbering) nft.numbering = numbering
        }
        return nft
    }
    return {}
}

export {parseMetaAttributes}
